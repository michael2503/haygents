<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PublicProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasExistingPhoto = !empty($this->user()?->professionalProfile?->profile_photo_path);

        return [
            'profile_photo' => [$hasExistingPhoto ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'display_name' => ['required', 'string', 'min:2', 'max:160'],
            'years_exp' => ['required', 'integer', 'min:0', 'max:80'],
            'main_area' => ['required', 'string', 'min:2', 'max:160'],
            'bio' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
