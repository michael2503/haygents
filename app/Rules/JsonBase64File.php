<?php

namespace App\Rules;

use App\Services\Base64FileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JsonBase64File implements ValidationRule
{
    public function __construct(
        private readonly array $allowedMimes = Base64FileService::DOCUMENT_MIMES,
        private readonly int $maxBytes = 5242880,
    ) {
    }

    public static function image(int $maxBytes = 5242880): self
    {
        return new self(Base64FileService::IMAGE_MIMES, $maxBytes);
    }

    public static function document(int $maxBytes = 5242880): self
    {
        return new self(Base64FileService::DOCUMENT_MIMES, $maxBytes);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(Base64FileService::class);
        $parsed = $service->inspect($value, $this->allowedMimes, $this->maxBytes);

        if ($parsed === null) {
            $extensions = implode(', ', array_unique(array_values($this->allowedMimes)));
            $maxMb = (int) round($this->maxBytes / 1048576);
            $fail("The {$attribute} must be a valid base64 encoded file ({$extensions}) not exceeding {$maxMb}MB.");
        }
    }
}
