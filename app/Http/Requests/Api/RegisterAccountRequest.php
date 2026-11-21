<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_type' => ['required', 'string', Rule::in(User::PUBLIC_REGISTER_TYPES)],
            'full_name' => ['required', 'string', 'min:2', 'max:160'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'dial_code' => ['required', 'string', 'regex:/^\+\d{1,5}$/'],
            'phone' => ['required', 'string', 'min:6', 'max:20', 'unique:users,phone'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'lga' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', Password::min(8)],
            'referral_code' => ['nullable', 'string', 'max:32'],
            'terms_accepted' => ['required', 'accepted'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_type.in' => 'Invalid account type selected. Only public marketplace roles can self-register.',
            'dial_code.regex' => 'Country dialer code must start with + followed by digits (e.g. +234).',
            'terms_accepted.accepted' => 'You must agree to the Terms of Service and Privacy Policy.',
        ];
    }
}
