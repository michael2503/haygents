<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class ResendVerificationCodeRequest extends BaseJsonApiRequest
{

    public function rules(): array
    {
        return [
            'channel' => ['required', 'string', Rule::in(['email', 'phone', 'both'])],
        ];
    }
}
