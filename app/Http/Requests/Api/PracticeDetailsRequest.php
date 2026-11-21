<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PracticeDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profile = $this->user()?->professionalProfile;
        $profession = $profile?->profession_type;
        $isFirm = $profile?->practice_mode === 'firm';
        $currentYear = (int) date('Y');

        if ($profession === 'lawyer') {
            return [
                'year_called_to_bar' => ['required', 'integer', 'min:1950', "max:{$currentYear}"],
                'nba_branch' => ['required', 'string', 'max:120'],
                'supreme_court_enrolment_number' => ['required', 'string', 'max:100'],
                'nba_seal_number' => ['required', 'string', 'max:100'],
                'firm_name' => [$isFirm ? 'required' : 'nullable', 'string', 'max:160'],
                'firm_cac_rc_number' => [$isFirm ? 'required' : 'nullable', 'string', 'max:80'],
            ];
        }

        if ($profession === 'surveyor') {
            return [
                'surcon_registration_number' => ['required', 'string', 'max:100'],
                'practising_licence_number' => ['required', 'string', 'max:100'],
                'year_registered' => ['required', 'integer', 'min:1950', "max:{$currentYear}"],
                'firm_name' => [$isFirm ? 'required' : 'nullable', 'string', 'max:160'],
                'firm_cac_rc_number' => [$isFirm ? 'required' : 'nullable', 'string', 'max:80'],
            ];
        }

        if ($profession === 'valuer') {
            return [
                'esvarbon_registration_number' => ['required', 'string', 'max:100'],
                'practising_licence_number' => ['required', 'string', 'max:100'],
                'niesv_membership' => ['required', 'string', Rule::in(['ANIVS', 'FNIVS'])],
                'firm_name' => [$isFirm ? 'required' : 'nullable', 'string', 'max:160'],
                'firm_cac_rc_number' => [$isFirm ? 'required' : 'nullable', 'string', 'max:80'],
            ];
        }

        return [
            'profession_type' => ['required'],
        ];
    }
}
