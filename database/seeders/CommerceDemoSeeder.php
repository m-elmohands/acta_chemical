<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommerceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::query()->updateOrCreate(
            ['email' => 'customer@actachemical.test'],
            [
                'name' => 'Demo Customer',
                'phone' => '+201000000099',
                'password' => Hash::make('password'),
            ],
        );

        $address = $customer->addresses()->withTrashed()->updateOrCreate(
            ['label' => 'Home'],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Hassan',
                'phone' => '+201000000099',
                'street' => 'Tahrir Street',
                'building' => '10',
                'floor' => '2',
                'apartment' => '4',
                'city' => 'Cairo',
                'state' => 'Cairo',
                'country' => 'EG',
                'delivery_notes' => 'Call before delivery.',
                'is_default' => true,
            ],
        );
        $address->restore();

        $products = Product::query()->whereIn('sku', [
            'ACTA-ACID-HCL-32',
            'ACTA-SOLV-ACETONE-AR',
            'ACTA-LAB-PH-BUFFER',
        ])->get()->keyBy('sku');

        $cart = $customer->cart()->firstOrCreate();
        $cart->items()->updateOrCreate(
            ['product_id' => $products['ACTA-ACID-HCL-32']->id],
            ['quantity' => 2],
        );
        $cart->items()->updateOrCreate(
            ['product_id' => $products['ACTA-SOLV-ACETONE-AR']->id],
            ['quantity' => 1],
        );

        $customer->wishlistProducts()->syncWithoutDetaching([
            $products['ACTA-LAB-PH-BUFFER']->id,
        ]);

        $order = Order::query()->updateOrCreate(
            ['reference' => '10000000-0000-4000-8000-000000000001'],
            [
                'user_id' => $customer->id,
                'address_id' => $address->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => PaymentMethod::CashOnDelivery->value,
                'subtotal_cents' => 239700,
                'delivery_fee_cents' => 0,
                'total_cents' => 239700,
                'currency' => 'EGP',
                'shipping_address' => $address->only([
                    'label', 'first_name', 'last_name', 'phone', 'street', 'building',
                    'floor', 'apartment', 'city', 'state', 'country', 'delivery_notes',
                ]),
                'customer_notes' => 'Demo cash-on-delivery order.',
                'placed_at' => now(),
            ],
        );

        $order->items()->updateOrCreate(
            ['sku' => 'ACTA-ACID-HCL-32'],
            [
                'product_id' => $products['ACTA-ACID-HCL-32']->id,
                'name_en' => $products['ACTA-ACID-HCL-32']->name_en,
                'name_ar' => $products['ACTA-ACID-HCL-32']->name_ar,
                'unit_price_cents' => 79900,
                'quantity' => 3,
                'line_total_cents' => 239700,
            ],
        );

        PaymentTransaction::query()->updateOrCreate(
            ['reference' => '20000000-0000-4000-8000-000000000001'],
            [
                'user_id' => $customer->id,
                'order_id' => $order->id,
                'order_reference' => $order->reference,
                'method' => PaymentMethod::CashOnDelivery,
                'status' => PaymentStatus::Pending,
                'amount_cents' => $order->total_cents,
                'currency' => 'EGP',
            ],
        );
    }
}
