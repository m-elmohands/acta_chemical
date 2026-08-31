<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'device_name' => ['required', 'string', 'max:100'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => ['description' => 'Customer display name.', 'example' => 'Ahmed Hassan'],
            'email' => ['description' => 'Unique customer email address.', 'example' => 'ahmed@example.com'],
            'phone' => ['description' => 'Optional unique Egyptian phone number.', 'example' => '+201000000000'],
            'password' => ['description' => 'At least eight characters containing letters and numbers.', 'example' => 'Password123'],
            'password_confirmation' => ['description' => 'Must match password.', 'example' => 'Password123'],
            'device_name' => ['description' => 'Name used to identify the issued token.', 'example' => 'Ahmed iPhone'],
        ];
    }
}
