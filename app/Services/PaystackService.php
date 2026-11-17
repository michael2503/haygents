<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PaystackService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $config = Configuration::first();
        $this->secretKey = $config->paystack_secret_key;
        $this->baseUrl = 'https://api.paystack.co';
    }

    /**
     * Initialize a Paystack transaction.
     * Never trusts frontend pricing; amount is passed in Kobo (Naira * 100).
     */
    public function initializeTransaction(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/json',
                'Cache-Control' => 'no-cache',
            ])->timeout(20)->post($this->baseUrl . '/transaction/initialize', [
                'email'        => $data['email'],
                'amount'       => (int) round($data['amount'] * 100), // Convert NGN to Kobo
                'reference'    => $data['reference'],
                'callback_url' => $data['callback_url'] ?? route('checkout.verify'),
                'metadata'     => $data['metadata'] ?? [],
            ]);

            if ($response->successful() && $response->json('status')) {
                return [
                    'success'           => true,
                    'authorization_url' => $response->json('data.authorization_url'),
                    'access_code'       => $response->json('data.access_code'),
                    'reference'         => $response->json('data.reference'),
                ];
            }

            Log::error('Paystack initialization failed', ['response' => $response->json()]);
            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Failed to initialize Paystack transaction.',
            ];
        } catch (Exception $e) {
            Log::error('Paystack Service Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Payment gateway connection error.',
            ];
        }
    }

    /**
     * Verify payment on the server side.
     * Prevents client-side tampering of payment confirmation.
     */
    public function verifyTransaction(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Cache-Control' => 'no-cache',
            ])->timeout(20)->get($this->baseUrl . '/transaction/verify/' . urlencode($reference));

            if ($response->successful() && $response->json('status')) {
                $data = $response->json('data');

                return [
                    'success'   => true,
                    'status'    => $data['status'], // 'success'
                    'reference' => $data['reference'],
                    'amount'    => $data['amount'] / 100, // Back to Naira
                    'currency'  => $data['currency'],
                    'paid_at'   => $data['paid_at'],
                    'channel'   => $data['channel'],
                    'raw'       => $data,
                ];
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Payment verification failed.',
            ];
        } catch (Exception $e) {
            Log::error('Paystack Verify Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Unable to verify payment with gateway.',
            ];
        }
    }

    /**
     * Validate Paystack Webhook HMAC-SHA512 signature.
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $computed = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computed, $signature);
    }
}
