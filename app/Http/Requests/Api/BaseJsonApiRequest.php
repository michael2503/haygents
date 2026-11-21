<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseJsonApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Strictly source validation data from the JSON request body (never URL query params).
     */
    public function validationData(): array
    {
        return $this->normalizeJsonValues($this->jsonPayload());
    }

    protected function jsonPayload(): array
    {
        $body = $this->isJson()
            ? array_replace($this->request->all(), $this->json()->all())
            : $this->request->all();

        if (!empty($body)) {
            return $body;
        }

        $raw = $this->getContent();
        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first() ?: 'The given data was invalid.',
            'errors' => $validator->errors()->toArray(),
        ], 422));
    }

    private function normalizeJsonValues(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $trimmed = trim($value);
                $data[$key] = $trimmed === '' ? null : $trimmed;
            } elseif (is_array($value)) {
                $data[$key] = $this->normalizeJsonValues($value);
            }
        }

        return $data;
    }
}
