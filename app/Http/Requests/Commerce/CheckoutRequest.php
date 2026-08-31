<?php

namespace App\Http\Requests\Commerce;

use App\Http\Requests\ApiFormRequest;
use App\Payments\Enums\PaymentMethod;
use Illuminate\Validation\Rule;

class CheckoutRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'address_id' => ['description' => 'An address owned by the authenticated customer.', 'example' => 1],
            'payment_method' => ['description' => 'Either paymob or cash_on_delivery.', 'example' => 'paymob'],
            'customer_notes' => ['description' => 'Optional notes saved with the order.', 'example' => 'Call before delivery'],
        ];
    }
}
