<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class LegalDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawDob = preg_replace('/\s+/', '', (string) $this->input('date_of_birth', ''));
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $rawDob, $m)) {
            try {
                $this->merge([
                    'date_of_birth' => Carbon::createFromFormat('d/m/Y', "{$m[1]}/{$m[2]}/{$m[3]}")->format('Y-m-d'),
                ]);
            } catch (\Throwable) {
                // Leave raw value for validation rule to reject
            }
        }
    }

    public function rules(): array
    {
        $eighteenYearsAgo = now()->subYears(18)->toDateString();

        return [
            'legal_full_name' => ['required', 'string', 'min:3', 'max:180'],
            'date_of_birth' => ['required', 'date', "before_or_equal:{$eighteenYearsAgo}"],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before_or_equal' => 'You must be at least 18 years old.',
        ];
    }
}
