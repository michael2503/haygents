<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessionalOfferingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $professionType = $this->user()?->professionalProfile?->profession_type;

        return [
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => [
                'required',
                'integer',
                Rule::exists('professional_services', 'id')->where(function ($query) use ($professionType) {
                    $query->where('is_active', true);
                    if ($professionType) {
                        $query->where('profession_type', $professionType);
                    }
                }),
            ],
            'states_covered' => ['required', 'array', 'min:1'],
            'states_covered.*' => ['required', 'string', 'max:80'],
            'service_modes' => ['required', 'array', 'min:1'],
            'service_modes.*' => ['required', 'string', Rule::in(['on-site', 'remote'])],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['required', 'string', 'max:60'],
            'typical_turnaround' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_ids.*.exists' => 'One or more selected services do not match your selected profession.',
        ];
    }
}
