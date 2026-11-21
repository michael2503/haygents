<?php

namespace App\Http\Requests\Api;

use App\Services\MobileOnboardingService;
use Illuminate\Foundation\Http\FormRequest;

class UploadCredentialsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profession = $this->user()?->professionalProfile?->profession_type;
        $definitions = MobileOnboardingService::CREDENTIAL_DEFINITIONS[$profession] ?? [];

        $rules = [];
        foreach (array_keys($definitions) as $docKey) {
            $rules[$docKey] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'];
        }

        return $rules;
    }
}
