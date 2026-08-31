<?php

namespace Tests\Feature\Seeders;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Database\Seeders\CommerceDemoSeeder;
use Database\Seeders\EcommerceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_commerce_seeders_are_idempotent_and_create_connected_demo_data(): void
    {
        $this->seed(EcommerceSeeder::class);
        $this->seed(CommerceDemoSeeder::class);
        $this->seed(EcommerceSeeder::class);
        $this->seed(CommerceDemoSeeder::class);

        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('products', 10);
        $this->assertDatabaseCount('addresses', 1);
        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseCount('cart_items', 2);
        $this->assertDatabaseCount('wishlists', 1);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseCount('payment_transactions', 1);

        $customer = User::query()->where('email', 'customer@actachemical.test')->firstOrFail();
        $order = Order::query()->firstOrFail();
        $payment = PaymentTransaction::query()->firstOrFail();

        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame($order->id, $payment->order_id);
        $this->assertSame($order->total_cents, $payment->amount_cents);
    }
}
