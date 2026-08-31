<?php

namespace Tests\Feature\Commerce;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileCommerceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_can_register_and_use_the_returned_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Mobile Customer',
            'email' => 'customer@example.com',
            'phone' => '+201000000001',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'device_name' => 'iPhone',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.user.email', 'customer@example.com')
            ->assertJsonStructure(['data' => ['token']]);

        $user = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('Password123', $user->password));
    }

    public function test_catalog_supports_search_category_filter_and_localized_output(): void
    {
        app()->setLocale('ar');
        $category = Category::factory()->create(['slug' => 'acids']);
        Product::factory()->for($category)->create([
            'name_en' => 'Sulfuric Acid',
            'name_ar' => 'حمض الكبريتيك',
            'price' => 250,
        ]);
        Product::factory()->create(['name_en' => 'Unrelated Product']);

        $this->getJson('/api/v1/products?category=acids&search=Sulfuric')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'حمض الكبريتيك')
            ->assertJsonPath('data.0.currency', 'EGP')
            ->assertJsonPath('meta.total', 1);
    }

    public function test_customer_can_manage_addresses_cart_and_wishlist(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $this->postJson('/api/v1/addresses', $this->addressData())
            ->assertCreated()
            ->assertJsonPath('data.is_default', true);

        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'quantity' => 2])
            ->assertOk()
            ->assertJsonPath('data.items.0.quantity', 2);

        $this->postJson("/api/v1/wishlist/{$product->slug}")->assertCreated();
        $this->getJson('/api/v1/wishlist')->assertOk()->assertJsonCount(1, 'data');
        $this->deleteJson("/api/v1/wishlist/{$product->slug}")->assertOk();
    }

    public function test_checkout_uses_server_prices_decrements_stock_and_creates_cod_payment(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $address = $user->addresses()->create($this->addressData());
        $product = Product::factory()->create([
            'price' => '125.50',
            'sale_price' => '100.00',
            'stock_quantity' => 5,
        ]);
        $user->cart()->create()->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $response = $this->postJson('/api/v1/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.order.subtotal_cents', 20000)
            ->assertJsonPath('data.order.total_cents', 20000)
            ->assertJsonPath('data.payment.status', 'pending')
            ->assertJsonPath('data.payment.checkout.type', 'cash_on_delivery');

        $this->assertSame(3, $product->refresh()->stock_quantity);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('payment_transactions', ['user_id' => $user->id, 'amount_cents' => 20000]);
    }

    public function test_customer_can_cancel_a_pending_order_and_inventory_is_restored(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $address = $user->addresses()->create($this->addressData());
        $product = Product::factory()->create(['stock_quantity' => 4]);
        $user->cart()->create()->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $reference = $this->postJson('/api/v1/checkout', [
            'address_id' => $address->id,
            'payment_method' => 'cash_on_delivery',
        ])->json('data.order.reference');

        $this->postJson("/api/v1/orders/{$reference}/cancel")
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertSame(4, $product->refresh()->stock_quantity);
    }

    public function test_customers_cannot_read_each_others_orders(): void
    {
        $owner = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $order = $this->orderFor($owner);
        Sanctum::actingAs($otherCustomer);

        $this->getJson("/api/v1/orders/{$order->reference}")->assertForbidden();
    }

    /** @return array<string, mixed> */
    private function addressData(): array
    {
        return [
            'label' => 'Home',
            'first_name' => 'Ahmed',
            'last_name' => 'Ali',
            'phone' => '+201000000000',
            'street' => 'Tahrir Street',
            'building' => '10',
            'floor' => '2',
            'apartment' => '4',
            'city' => 'Cairo',
            'state' => 'Cairo',
            'country' => 'EG',
            'is_default' => true,
        ];
    }

    private function orderFor(User $user): Order
    {
        return $user->orders()->create([
            'reference' => fake()->uuid(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'subtotal_cents' => 10000,
            'delivery_fee_cents' => 0,
            'total_cents' => 10000,
            'currency' => 'EGP',
            'shipping_address' => $this->addressData(),
            'placed_at' => now(),
        ]);
    }
}
