<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class OnboardStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:160'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'dial_code' => ['nullable', 'string', 'regex:/^\+\d{1,5}$/'],
            'phone' => ['required', 'string', 'min:6', 'max:20', 'unique:users,phone'],
            'country' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'lga' => ['nullable', 'string', 'max:120'],
            'staff_role' => ['required', 'string', Rule::in(User::STAFF_ROLES)],
            'password' => ['required', 'string', Password::min(8)],
        ];
    }
}
