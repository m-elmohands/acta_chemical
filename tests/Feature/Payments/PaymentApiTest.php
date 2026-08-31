<?php

namespace Tests\Feature\Payments;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_paymob_and_cash_on_delivery_in_egp(): void
    {
        $this->getJson('/api/v1/payment-methods')
            ->assertOk()
            ->assertJsonPath('data.0.code', 'paymob')
            ->assertJsonPath('data.0.currency', 'EGP')
            ->assertJsonPath('data.1.code', 'cash_on_delivery')
            ->assertJsonPath('data.1.currency', 'EGP');
    }

    public function test_it_creates_a_cash_on_delivery_payment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $order = $this->createOrder($user, 35075, 'cash_on_delivery');

        $response = $this->postJson('/api/v1/payments', [
            'order_reference' => $order->reference,
            'method' => 'cash_on_delivery',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.transaction.order_reference', $order->reference)
            ->assertJsonPath('data.transaction.method', 'cash_on_delivery')
            ->assertJsonPath('data.transaction.amount_cents', 35075)
            ->assertJsonPath('data.transaction.currency', 'EGP')
            ->assertJsonPath('data.checkout.type', 'cash_on_delivery');

        $this->assertDatabaseHas('payment_transactions', [
            'order_reference' => $order->reference,
            'status' => PaymentStatus::Pending->value,
            'amount_cents' => 35075,
        ]);
    }

    public function test_it_creates_a_paymob_intention_without_exposing_the_secret_key(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $order = $this->createOrder($user, 10000, 'paymob');

        config()->set('services.paymob', [
            'base_url' => 'https://accept.paymob.com',
            'checkout_url' => 'https://accept.paymob.com/unifiedcheckout',
            'secret_key' => 'paymob-secret',
            'public_key' => 'paymob-public',
            'hmac_secret' => 'hmac-secret',
            'integration_id' => 12345,
            'redirect_url' => 'https://example.com/payment-result',
        ]);

        Http::fake([
            'accept.paymob.com/v1/intention/' => Http::response([
                'id' => 'pi_123',
                'client_secret' => 'client-secret-123',
            ]),
        ]);

        $response = $this->postJson('/api/v1/payments', [
            'order_reference' => $order->reference,
            'method' => 'paymob',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.checkout.client_secret', 'client-secret-123')
            ->assertJsonPath('data.checkout.public_key', 'paymob-public')
            ->assertJsonMissing(['secret_key' => 'paymob-secret']);

        Http::assertSent(fn ($request): bool => $request->url() === 'https://accept.paymob.com/v1/intention/'
            && $request->hasHeader('Authorization', 'Bearer paymob-secret')
            && $request['amount'] === 10000
            && $request['currency'] === 'EGP'
            && $request['payment_methods'] === [12345]);
    }

    public function test_payment_initiation_requires_authentication(): void
    {
        $this->postJson('/api/v1/payments', [
            'order_reference' => 'ORDER-1003',
            'method' => 'paymob',
        ])->assertUnauthorized()
            ->assertJsonPath('data', null)
            ->assertJsonPath('message', 'Authentication is required.');
    }

    public function test_it_rejects_a_paymob_callback_with_an_invalid_hmac(): void
    {
        $this->postJson('/api/v1/payments/paymob/webhook?hmac=invalid', [
            'type' => 'TRANSACTION',
            'obj' => ['id' => 123],
        ])->assertUnprocessable();
    }

    public function test_a_valid_paymob_callback_marks_the_payment_as_paid(): void
    {
        config()->set('services.paymob.hmac_secret', 'hmac-secret');
        $user = User::factory()->create();
        $order = $this->createOrder($user, 10000, 'paymob');

        $transaction = PaymentTransaction::factory()->create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'method' => PaymentMethod::Paymob,
            'gateway_reference' => '9001',
        ]);

        $object = $this->paymobCallbackObject($transaction->reference);
        $hmac = $this->paymobHmac($object, 'hmac-secret');

        $this->postJson('/api/v1/payments/paymob/webhook?hmac='.$hmac, [
            'type' => 'TRANSACTION',
            'obj' => $object,
        ])->assertOk()
            ->assertJsonPath('data.status', 'paid');

        $transaction->refresh();

        $this->assertSame(PaymentStatus::Paid, $transaction->status);
        $this->assertSame('7001', $transaction->gateway_transaction_id);
        $this->assertNotNull($transaction->paid_at);
        $this->assertSame('paid', $order->refresh()->payment_status);
    }

    /** @return array<string, mixed> */
    private function paymobCallbackObject(string $reference): array
    {
        return [
            'amount_cents' => 10000,
            'created_at' => '2026-08-30T12:00:00',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => 7001,
            'integration_id' => 12345,
            'is_3d_secure' => true,
            'is_auth' => false,
            'is_capture' => false,
            'is_refunded' => false,
            'is_standalone_payment' => true,
            'is_voided' => false,
            'order' => ['id' => 9001, 'merchant_order_id' => $reference],
            'owner' => 1,
            'pending' => false,
            'source_data' => ['pan' => '2346', 'sub_type' => 'MasterCard', 'type' => 'card'],
            'success' => true,
        ];
    }

    /** @param array<string, mixed> $object */
    private function paymobHmac(array $object, string $secret): string
    {
        $keys = [
            'amount_cents', 'created_at', 'currency', 'error_occured',
            'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure',
            'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment',
            'is_voided', 'order.id', 'owner', 'pending', 'source_data.pan',
            'source_data.sub_type', 'source_data.type', 'success',
        ];

        $value = collect($keys)->map(function (string $key) use ($object): string {
            $item = data_get($object, $key, '');

            return match (true) {
                $item === true => 'true',
                $item === false => 'false',
                default => (string) $item,
            };
        })->implode('');

        return hash_hmac('sha512', $value, $secret);
    }

    private function createOrder(User $user, int $totalCents, string $paymentMethod): Order
    {
        return $user->orders()->create([
            'reference' => (string) Str::uuid(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $paymentMethod,
            'subtotal_cents' => $totalCents,
            'delivery_fee_cents' => 0,
            'total_cents' => $totalCents,
            'currency' => 'EGP',
            'shipping_address' => [
                'first_name' => 'Ahmed',
                'last_name' => 'Ali',
                'email' => $user->email,
                'phone' => '+201000000000',
                'street' => 'Test Street',
                'building' => '1',
                'floor' => '1',
                'apartment' => '1',
                'city' => 'Cairo',
                'state' => 'Cairo',
                'country' => 'EG',
            ],
            'placed_at' => now(),
        ]);
    }
}
