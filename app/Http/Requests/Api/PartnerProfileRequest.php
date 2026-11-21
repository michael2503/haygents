<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PartnerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCompany = $this->user()?->account_type === 'company';

        return [
            'display_name' => ['required', 'string', 'min:2', 'max:160'],
            'role_title' => ['nullable', 'string', 'max:160'],
            'years_exp' => ['nullable', 'integer', 'min:0', 'max:80'],
            'main_area' => ['required', 'string', 'max:160'],
            'bio' => ['required', 'string', 'min:10', 'max:2000'],
            'credentials' => ['nullable', 'string', 'max:255'],
            'cac_rc_number' => [$isCompany ? 'required' : 'nullable', 'string', 'max:80'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'cac_certificate' => [$isCompany ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
