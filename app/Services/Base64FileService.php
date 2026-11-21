<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Base64FileService
{
    public const IMAGE_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    public const DOCUMENT_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];

    /**
     * Validate a JSON-supplied base64/data-URI string, JSON file object, or UploadedFile
     * and return decoded metadata without touching disk yet.
     *
     * @return array{binary: string, mime: string, extension: string, original_filename: string, size: int}|null
     */
    public function inspect(mixed $input, array $allowedMimes = self::DOCUMENT_MIMES, int $maxBytes = 5242880): ?array
    {
        if ($input instanceof UploadedFile) {
            if (!$input->isValid()) {
                return null;
            }
            $binary = file_get_contents($input->getRealPath());
            if ($binary === false || strlen($binary) === 0 || strlen($binary) > $maxBytes) {
                return null;
            }
            $mime = $this->detectMimeFromBinary($binary);
            if (!isset($allowedMimes[$mime])) {
                return null;
            }

            return [
                'binary' => $binary,
                'mime' => $mime,
                'extension' => $allowedMimes[$mime],
                'original_filename' => $this->sanitizeFilename($input->getClientOriginalName(), $allowedMimes[$mime]),
                'size' => strlen($binary),
            ];
        }

        $rawBase64 = null;
        $providedFilename = null;

        if (is_string($input)) {
            $rawBase64 = trim($input);
        } elseif (is_array($input)) {
            $candidate = $input['base64'] ?? $input['data'] ?? $input['content'] ?? null;
            if (!is_string($candidate)) {
                return null;
            }
            $rawBase64 = trim($candidate);
            $nameCandidate = $input['filename'] ?? $input['name'] ?? $input['original_filename'] ?? null;
            if (is_string($nameCandidate)) {
                $providedFilename = $nameCandidate;
            }
        } else {
            return null;
        }

        if ($rawBase64 === '') {
            return null;
        }

        // Strip optional data URI prefix: data:image/jpeg;base64,
        if (str_starts_with($rawBase64, 'data:')) {
            if (!preg_match('/^data:([a-zA-Z0-9.+\-\/]+);base64,(.+)$/s', $rawBase64, $matches)) {
                return null;
            }
            $rawBase64 = $matches[2];
        }

        // Strip whitespace/newlines introduced by some mobile base64 encoders
        $cleanBase64 = preg_replace('/\s+/', '', $rawBase64);
        if (!is_string($cleanBase64) || $cleanBase64 === '') {
            return null;
        }

        // Guard against oversized payloads before full decoding
        $maxEncodedLength = (int) ceil(($maxBytes / 3) * 4) + 16;
        if (strlen($cleanBase64) > $maxEncodedLength) {
            return null;
        }

        $binary = base64_decode($cleanBase64, true);
        if ($binary === false) {
            return null;
        }

        $size = strlen($binary);
        if ($size === 0 || $size > $maxBytes) {
            return null;
        }

        $mime = $this->detectMimeFromBinary($binary);
        if (!isset($allowedMimes[$mime])) {
            return null;
        }

        $extension = $allowedMimes[$mime];

        return [
            'binary' => $binary,
            'mime' => $mime,
            'extension' => $extension,
            'original_filename' => $this->sanitizeFilename($providedFilename, $extension),
            'size' => $size,
        ];
    }

    /**
     * Store a validated JSON base64 file (or UploadedFile) to the specified disk and directory.
     *
     * @return array{path: string, original_filename: string, mime_type: string, file_size: int}
     */
    public function store(
        mixed $input,
        string $directory,
        string $disk = 'local',
        ?string $oldPath = null,
        array $allowedMimes = self::DOCUMENT_MIMES,
        int $maxBytes = 5242880,
        string $fieldName = 'file'
    ): array {
        $parsed = $this->inspect($input, $allowedMimes, $maxBytes);

        if (!$parsed) {
            throw ValidationException::withMessages([
                $fieldName => ['Invalid or unsupported base64 file payload. Allowed types: ' . implode(', ', array_unique(array_values($allowedMimes))) . ' up to ' . round($maxBytes / 1048576, 1) . 'MB.'],
            ]);
        }

        if ($oldPath) {
            Storage::disk($disk)->delete($oldPath);
        }

        $cleanDirectory = trim($directory, '/');
        $filename = Str::uuid()->toString() . '.' . $parsed['extension'];
        $path = ($cleanDirectory !== '' ? $cleanDirectory . '/' : '') . $filename;

        Storage::disk($disk)->put($path, $parsed['binary']);

        return [
            'path' => $path,
            'original_filename' => $parsed['original_filename'],
            'mime_type' => $parsed['mime'],
            'file_size' => $parsed['size'],
        ];
    }

    private function detectMimeFromBinary(string $binary): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detected = strtolower((string) $finfo->buffer($binary));

        return match ($detected) {
            'image/jpg', 'image/pjpeg' => 'image/jpeg',
            default => $detected,
        };
    }

    private function sanitizeFilename(?string $filename, string $extension): string
    {
        if (!$filename || trim($filename) === '') {
            return 'upload_' . Str::lower(Str::random(8)) . '.' . $extension;
        }

        $base = basename(str_replace('\\', '/', $filename));
        $nameOnly = pathinfo($base, PATHINFO_FILENAME);
        $safe = preg_replace('/[^a-zA-Z0-9._\-]/', '_', (string) $nameOnly);
        $safe = trim((string) $safe, '._-');

        if ($safe === '') {
            $safe = 'upload_' . Str::lower(Str::random(8));
        }

        return Str::limit($safe, 80, '') . '.' . $extension;
    }
}
