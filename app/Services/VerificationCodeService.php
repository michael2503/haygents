<?php

namespace App\Services;

use App\Mail\VerificationEmail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class VerificationCodeService
{
    private const EXPIRY_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;

    public function sendEmailCode(User $user): void
    {
        $code = $this->issueCode($user, 'email', $user->email);

        try {
            $mailData = [
                "code" => $code,
                'exp' => self::EXPIRY_MINUTES
            ];
            Mail::to($user->email)->send(new VerificationEmail($mailData));

            // Mail::raw(
            //     "Hello {$user->name},\n\nYour HAYGENTS email verification code is: {$code}\nThis code expires in " . self::EXPIRY_MINUTES . " minutes.\n\nIf you did not request this, please ignore this email.",
            //     function ($message) use ($user) {
            //         $message->to($user->email, $user->name)
            //             ->subject('HAYGENTS Email Verification Code');
            //     }
            // );
        } catch (Throwable $e) {
            Log::warning('Unable to dispatch verification email via mailer.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function sendPhoneCode(User $user): void
    {
        $target = trim(($user->dial_code ?? '') . ' ' . ($user->phone ?? ''));
        $code = $this->issueCode($user, 'phone', $target);

        // Hook SMS gateway provider here (e.g., Termii, Twilio, Africa's Talking).
        Log::info('SMS verification code queued for dispatch.', [
            'user_id' => $user->id,
            'channel' => 'phone',
            'target_masked' => $this->maskTarget($target),
        ]);
    }

    public function verifyCode(User $user, string $channel, string $plainCode): bool
    {
        $record = VerificationCode::where('user_id', $user->id)
            ->where('channel', $channel)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'code' => ["No active {$channel} verification code was found. Please request a new code."],
            ]);
        }

        if ($record->expires_at < now()) {
            throw ValidationException::withMessages([
                'code' => ["This {$channel} verification code has expired. Please request a new code."],
            ]);
        }

        if ($record->attempts >= self::MAX_ATTEMPTS) {
            throw ValidationException::withMessages([
                'code' => ["Too many invalid attempts. Please request a new {$channel} verification code."],
            ]);
        }

        if (!Hash::check(trim($plainCode), $record->code_hash)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'code' => ["The {$channel} verification code is incorrect."],
            ]);
        }

        $record->forceFill(['verified_at' => now()])->save();

        return true;
    }

    private function issueCode(User $user, string $channel, string $target): string
    {
        VerificationCode::where('user_id', $user->id)
            ->where('channel', $channel)
            ->whereNull('verified_at')
            ->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        VerificationCode::create([
            'user_id' => $user->id,
            'channel' => $channel,
            'target' => $target,
            'code_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        return $code;
    }

    private function maskTarget(string $target): string
    {
        $len = strlen($target);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        return substr($target, 0, 3) . str_repeat('*', max(1, $len - 6)) . substr($target, -3);
    }
}
