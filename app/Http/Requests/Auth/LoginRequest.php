<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;

class LoginRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => ['description' => 'Registered customer email address.', 'example' => 'ahmed@example.com'],
            'password' => ['description' => 'Customer password.', 'example' => 'Password123'],
            'device_name' => ['description' => 'Name used to identify the issued token.', 'example' => 'Ahmed iPhone'],
        ];
    }
}
