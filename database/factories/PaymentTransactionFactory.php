<?php

namespace Database\Factories;

use App\Models\PaymentTransaction;
use App\Payments\Enums\PaymentMethod;
use App\Payments\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference' => (string) Str::uuid(),
            'order_reference' => fake()->unique()->bothify('ORD-####-????'),
            'method' => PaymentMethod::CashOnDelivery,
            'status' => PaymentStatus::Pending,
            'amount_cents' => fake()->numberBetween(100, 1000000),
            'currency' => 'EGP',
        ];
    }
}
