<?php

namespace App\Http\Requests\Payments;

use App\Http\Requests\ApiFormRequest;
use App\Payments\Enums\PaymentMethod;
use Illuminate\Validation\Rule;

class InitiatePaymentRequest extends ApiFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'order_reference' => ['required', 'string', 'max:100'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'order_reference' => ['description' => 'Reference of an owned pending order.', 'example' => '10000000-0000-4000-8000-000000000001'],
            'method' => ['description' => 'Must match the payment method selected at checkout.', 'example' => 'paymob'],
        ];
    }
}
