<?php

namespace App\Http\Requests\Commerce;

use App\Http\Requests\ApiFormRequest;

class AddressRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'street' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['sometimes', 'string', 'size:2', 'in:EG'],
            'delivery_notes' => ['nullable', 'string', 'max:1000'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'label' => ['description' => 'Customer-facing address label.', 'example' => 'Home'],
            'first_name' => ['description' => 'Recipient first name.', 'example' => 'Ahmed'],
            'last_name' => ['description' => 'Recipient last name.', 'example' => 'Hassan'],
            'phone' => ['description' => 'Delivery contact number.', 'example' => '+201000000000'],
            'street' => ['description' => 'Street name and delivery details.', 'example' => '10 Tahrir Street'],
            'building' => ['description' => 'Building number.', 'example' => '10'],
            'floor' => ['description' => 'Floor number.', 'example' => '2'],
            'apartment' => ['description' => 'Apartment number.', 'example' => '4'],
            'city' => ['description' => 'Egyptian city.', 'example' => 'Cairo'],
            'state' => ['description' => 'Egyptian governorate.', 'example' => 'Cairo'],
            'country' => ['description' => 'Two-letter country code. Only EG is accepted.', 'example' => 'EG'],
            'delivery_notes' => ['description' => 'Optional delivery instructions.', 'example' => 'Call before delivery'],
            'is_default' => ['description' => 'Make this the default delivery address.', 'example' => true],
        ];
    }
}
