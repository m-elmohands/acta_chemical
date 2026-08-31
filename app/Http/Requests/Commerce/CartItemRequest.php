<?php

namespace App\Http\Requests\Commerce;

use App\Http\Requests\ApiFormRequest;

class CartItemRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required_without:quantity', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'product_id' => ['description' => 'Product ID. Required when adding an item.', 'example' => 1],
            'quantity' => ['description' => 'Requested quantity between 1 and 100.', 'example' => 2],
        ];
    }
}
