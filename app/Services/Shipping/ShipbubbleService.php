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
     * @param array $package Package details
     * @return array|null
     */
    public function fetchRates(array $sender, array $receiver, array $package): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/rates', [
                    'sender' => $sender,
                    'receiver' => $receiver,
                    'package' => $package,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Shipbubble fetchRates API Error', [
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
}
