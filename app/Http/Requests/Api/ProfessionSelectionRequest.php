<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfessionSelectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profession_type' => ['required', 'string', Rule::in(['lawyer', 'surveyor', 'valuer'])],
            'practice_mode' => ['required', 'string', Rule::in(['individual', 'firm', 'on_my_own', 'as_part_of_a_firm'])],
        ];
    }
}
