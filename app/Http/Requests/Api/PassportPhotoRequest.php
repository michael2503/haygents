<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PassportPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'passport_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'capture_source' => ['required', 'string', Rule::in(['camera'])],
        ];
    }

    public function messages(): array
    {
        return [
            'capture_source.in' => 'Passport photograph must be taken with your camera. Gallery uploads are not accepted.',
        ];
    }
}
