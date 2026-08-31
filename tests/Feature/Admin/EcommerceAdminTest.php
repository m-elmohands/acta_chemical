<?php

namespace Tests\Feature\Admin;

use App\Commerce\Actions\TransitionOrderStatus;
use App\Commerce\Enums\OrderStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Payments\Actions\ConfirmCashOnDeliveryPayment;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use Database\Seeders\AdminAuthorizationSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class EcommerceAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_role_can_access_admin_panel(): void
    {
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);
        $customer = User::factory()->create();
        $this->seed(AdminAuthorizationSeeder::class);
        $panel = Filament::getPanel('admin');

        $this->assertTrue($admin->fresh()->canAccessPanel($panel));
        $this->assertFalse($customer->canAccessPanel($panel));
        $this->assertTrue($admin->can('manage products'));
    }

    public function test_admin_can_open_ecommerce_management_pages(): void
    {
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);
        $this->seed(AdminAuthorizationSeeder::class);
        $this->actingAs($admin->fresh());

        foreach (['/admin', '/admin/categories', '/admin/categories/create', '/admin/products', '/admin/products/create', '/admin/orders', '/admin/payment-transactions', '/admin/customers'] as $url) {
            $this->get($url)->assertSuccessful();
        }
    }

    public function test_admin_can_follow_the_order_workflow_and_the_change_is_audited(): void
    {
        [$admin, $order] = $this->adminAndOrder();

        app(TransitionOrderStatus::class)->execute($order, OrderStatus::Confirmed, $admin);

        $this->assertSame(OrderStatus::Confirmed, $order->fresh()->status);
        $this->assertDatabaseHas('activity_log', [
            'causer_id' => $admin->id,
            'subject_id' => $order->id,
            'description' => 'Order status changed',
        ]);
    }

    public function test_admin_can_view_an_order_with_an_array_shipping_address(): void
    {
        [$admin, $order] = $this->adminAndOrder();

        $this->actingAs($admin)
            ->get("/admin/orders/{$order->reference}")
            ->assertSuccessful()
            ->assertSee('Cairo');
    }

    public function test_admin_can_confirm_a_pending_cash_on_delivery_payment(): void
    {
        [$admin, $order] = $this->adminAndOrder();
        $payment = PaymentTransaction::factory()->create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'order_reference' => $order->reference,
            'method' => PaymentMethod::CashOnDelivery,
            'status' => PaymentStatus::Pending,
        ]);

        app(ConfirmCashOnDeliveryPayment::class)->execute($payment, $admin);

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(PaymentStatus::Paid->value, $order->fresh()->payment_status);
        $this->assertTrue(Activity::where('description', 'Cash-on-delivery payment confirmed')->exists());
    }

    /** @return array{User, Order} */
    private function adminAndOrder(): array
    {
        $admin = User::factory()->create(['email' => 'admin@gmail.com']);
        $customer = User::factory()->create();
        $this->seed(AdminAuthorizationSeeder::class);

        $order = Order::create([
            'reference' => (string) Str::uuid(),
            'user_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'payment_status' => PaymentStatus::Pending->value,
            'payment_method' => PaymentMethod::CashOnDelivery->value,
            'subtotal_cents' => 10000,
            'delivery_fee_cents' => 0,
            'total_cents' => 10000,
            'currency' => 'EGP',
            'shipping_address' => ['city' => 'Cairo'],
            'placed_at' => now(),
        ]);

        return [$admin->fresh(), $order];
    }
}
