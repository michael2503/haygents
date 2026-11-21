<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GovernmentIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_type' => ['required', 'string', Rule::in(['nin', 'international_passport', 'drivers_licence', 'voters_card'])],
            'id_number' => ['required', 'string', 'min:5', 'max:80'],
            'id_front_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'id_back_file' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('id_type') !== 'international_passport'),
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id_back_file.required' => 'Back of ID upload is required for NIN, Driver\'s licence, and Voter\'s card.',
        ];
    }
}
