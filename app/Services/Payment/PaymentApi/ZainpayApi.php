<?php

namespace App\Services\Payment\PaymentApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ZainpayApi
{
    private static $instance = null;
    private string $publicKey;
    private string $baseUrl;

    private function __construct()
    {
        $rawKey = \App\Models\Setting::whereIn('key', ['zainpay_token', 'zainpayToken'])->value('value') ?: config('services.zainpay.public_key', '');
        $this->publicKey = trim($rawKey);

        if (empty($this->publicKey) || str_contains($this->publicKey, '...')) {
            throw new Exception('Zainpay Token/Key is missing or set to placeholder in Admin Settings.');
        }

        $isLive = filter_var(\App\Models\Setting::where('key', 'zainpay_mode_live')->value('value'), FILTER_VALIDATE_BOOLEAN);
        $this->baseUrl = $isLive ? 'https://api.zainpay.ng' : 'https://sandbox.zainpay.ng';
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
            'Authorization' => 'Bearer ' . $this->publicKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/' . ltrim($endpoint, '/'), $body);

        if (!$response->successful()) {
            $this->handleError($response);
        }

        return $response->json();
    }

    public function get(string $endpoint, array $query = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->publicKey,
            'Accept'        => 'application/json',
        ])->get($this->baseUrl . '/' . ltrim($endpoint, '/'), $query);

        if (!$response->successful()) {
            $this->handleError($response);
        }

        return $response->json();
    }

    private function handleError($response)
    {
        $status = $response->status();
        $body = $response->body();
        Log::error("Zainpay API Error [Status: $status]: $body");
        
        $data = $response->json();
        $message = $data['description'] ?? $data['message'] ?? 'Zainpay API request failed';
        throw new Exception('Zainpay API error: ' . $message);
    }
}
