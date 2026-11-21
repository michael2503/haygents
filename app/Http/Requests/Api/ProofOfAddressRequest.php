<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProofOfAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawDate = preg_replace('/\s+/', '', (string) $this->input('bill_date', ''));
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $rawDate, $m)) {
            try {
                $this->merge([
                    'bill_date' => Carbon::createFromFormat('d/m/Y', "{$m[1]}/{$m[2]}/{$m[3]}")->format('Y-m-d'),
                ]);
            } catch (\Throwable) {
                // Leave raw value for validation rule to reject
            }
        }
    }

    public function rules(): array
    {
        $threeMonthsAgo = now()->subMonths(3)->toDateString();
        $today = now()->toDateString();

        return [
            'proof_of_address_type' => ['required', 'string', Rule::in(['electricity_bill', 'waste_bill', 'bank_statement'])],
            'bill_date' => ['required', 'date', "after_or_equal:{$threeMonthsAgo}", "before_or_equal:{$today}"],
            'proof_of_address_file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'bill_date.after_or_equal' => 'Proof of address must be dated within the last 3 months.',
            'bill_date.before_or_equal' => 'Document date cannot be in the future.',
        ];
    }
}
