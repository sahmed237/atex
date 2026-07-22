<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShipbubbleService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.shipbubble.url', 'https://api.shipbubble.com/v1');
        $this->apiKey = config('services.shipbubble.key', '');
    }

    /**
     * Fetch shipping rates for a parcel between origin and destination addresses.
     *
     * @param array $sender Sender details
     * @param array $receiver Receiver details
     * @param array $packageItems Package items
     * @param array $packageDimension Package dimensions
     * @return array|null
     */
    public function fetchRates(array $sender, array $receiver, array $packageItems, array $packageDimension): ?array
    {
        try {
            // 1. Validate Sender Address
            $senderValidation = $this->validateAddress([
                'name' => $sender['name'],
                'email' => $sender['email'] ?? 'sender@example.com',
                'phone' => $sender['phone'],
                'address' => $sender['address'] . ', ' . $sender['city'] . ', ' . $sender['state'] . ', ' . $sender['country']
            ]);
            
            // 2. Validate Receiver Address
            $receiverValidation = $this->validateAddress([
                'name' => $receiver['name'],
                'email' => $receiver['email'] ?? 'receiver@example.com',
                'phone' => $receiver['phone'],
                'address' => $receiver['address'] . ', ' . $receiver['city'] . ', ' . $receiver['state'] . ', ' . $receiver['country']
            ]);

            if (!$senderValidation || !isset($senderValidation['data']['address_code'])) {
                Log::error('Shipbubble Sender Validation Failed', ['response' => $senderValidation]);
                return null;
            }
            if (!$receiverValidation || !isset($receiverValidation['data']['address_code'])) {
                Log::error('Shipbubble Receiver Validation Failed', ['response' => $receiverValidation]);
                return null;
            }

            $senderCode = $senderValidation['data']['address_code'];
            $receiverCode = $receiverValidation['data']['address_code'];

            // 3. Fetch Rates
            $payload = [
                'sender_address_code' => $senderCode,
                'reciever_address_code' => $receiverCode,
                'pickup_date' => date('Y-m-d', strtotime('+1 day')),
                'category_id' => 20754594, // Valid category: Light weight items
                'package_items' => $packageItems,
                'package_dimension' => $packageDimension,
                'delivery_instructions' => 'Handle with care'
            ];

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/shipping/fetch_rates', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Shipbubble fetch_rates API Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Shipbubble fetchRates Exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Validate a physical address in Nigeria
     * 
     * @param array $data ['name', 'email', 'phone', 'address']
     * @return array|null
     */
    public function validateAddress(array $data): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/shipping/address/validate', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Shipbubble validateAddress API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'data' => $data
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Shipbubble validateAddress Exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get list of available couriers
     * 
     * @return array|null
     */
    public function getCouriers(): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->get($this->baseUrl . '/couriers');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create a shipment / generate a label
     * 
     * @param array $payload
     * @return array|null
     */
    public function createShipment(array $payload): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/shipping/labels', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Track a shipment
     * 
     * @param string $trackingId
     * @return array|null
     */
    public function trackShipment(string $trackingId): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->get($this->baseUrl . '/tracking/' . $trackingId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
