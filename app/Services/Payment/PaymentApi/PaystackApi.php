<?php

namespace App\Services\Payment\PaymentApi;

use Illuminate\Support\Facades\Http;
use Exception;

class PaystackApi
{
    private static $instance = null;
    private string $secretKey;
    private string $baseUrl;

    private function __construct()
    {
        $rawKey = \App\Models\Setting::where('key', 'paystack_secret_key')->value('value') ?: config('services.paystack.secret_key', '');
        $this->secretKey = trim($rawKey);

        if (empty($this->secretKey) || str_contains($this->secretKey, '...')) {
            throw new Exception('Paystack Secret Key is missing or set to placeholder in Admin Settings. Please set your actual Paystack Secret Key under Admin -> Settings -> Payment Gateways.');
        }

        $this->baseUrl = 'https://api.paystack.co';
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function post(string $endpoint, array $body = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/' . ltrim($endpoint, '/'), $body);

        if (!$response->successful()) {
            $this->handleError($response);
        }

        return $response->json();
    }

    public function get(string $endpoint, array $query = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Accept' => 'application/json',
        ])->get($this->baseUrl . '/' . ltrim($endpoint, '/'), $query);

        if (!$response->successful()) {
            $this->handleError($response);
        }

        return $response->json();
    }

    private function handleError($response)
    {
        $data = $response->json();
        $message = $data['message'] ?? 'Paystack API request failed';
        throw new Exception('Paystack API error: ' . $message);
    }
}
