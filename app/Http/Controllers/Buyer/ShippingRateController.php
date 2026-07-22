<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Shipping\ShipbubbleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingRateController extends Controller
{
    protected ShipbubbleService $shipbubbleService;

    public function __construct(ShipbubbleService $shipbubbleService)
    {
        $this->shipbubbleService = $shipbubbleService;
    }

    /**
     * Calculate shipping rates dynamically using Shipbubble API.
     */
    public function calculateRates(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'receiver' => 'required|array',
            'receiver.name' => 'required|string|max:255',
            'receiver.phone' => 'required|string|max:50',
            'receiver.address' => 'required|string|max:255',
            'receiver.city' => 'required|string|max:100',
            'receiver.state' => 'required|string|max:100',
            'receiver.country' => 'required|string|max:10',
        ]);

        $items = $request->input('items');
        $receiverData = $request->input('receiver');

        // Resolve Sender (Origin) from the first item in the cart
        $firstItem = $items[0];
        $product = Product::with('sellerProfile')->find($firstItem['id']);
        
        $sender = [
            'name' => 'Adamawa Trade Platform',
            'phone' => '08012345678',
            'email' => 'shipping@atex.gov.ng',
            'address' => 'Trade Center, Jimeta',
            'city' => 'Yola',
            'state' => 'Adamawa',
            'country' => 'NG'
        ];

        if ($product && $product->sellerProfile) {
            $sp = $product->sellerProfile;
            $sender['name'] = $sp->business_name ?: $sender['name'];
            $sender['phone'] = $sp->phone ?: $sender['phone'];
            $sender['address'] = $sp->address ?: $sender['address'];
            $sender['city'] = $sp->city ?: ($sp->lga ?: $sender['city']);
            $sender['state'] = $sp->state ?: $sender['state'];
            $sender['country'] = $sp->country ?: $sender['country'];
        }

        // Calculate aggregate package parameters
        $totalWeight = 0;
        $maxLength = 0;
        $maxWidth = 0;
        $totalHeight = 0;

        foreach ($items as $item) {
            $p = Product::find($item['id']);
            if ($p) {
                $qty = (int)$item['qty'];
                $itemWeight = (float)($p->weight ?: 1.0); // Fallback to 1kg
                $totalWeight += $itemWeight * $qty;

                $maxLength = max($maxLength, (float)($p->length ?: 15.0));
                $maxWidth = max($maxWidth, (float)($p->width ?: 15.0));
                $totalHeight += (float)($p->height ?: 10.0) * $qty;
            }
        }

        $package = [
            'weight' => $totalWeight ?: 1.0,
            'length' => $maxLength ?: 15.0,
            'width' => $maxWidth ?: 15.0,
            'height' => $totalHeight ?: 10.0,
            'value' => 1000,
            'description' => 'E-commerce goods shipment'
        ];

        // Format Receiver structure for Shipbubble
        $receiver = [
            'name' => $receiverData['name'],
            'phone' => $receiverData['phone'],
            'email' => $receiverData['email'] ?? 'buyer@atex.gov.ng',
            'address' => $receiverData['address'],
            'city' => $receiverData['city'],
            'state' => $receiverData['state'],
            'country' => $receiverData['country']
        ];

        Log::info('Shipbubble fetchRates request payload', [
            'sender' => $sender,
            'receiver' => $receiver,
            'package' => $package
        ]);

        // Call Service
        $result = $this->shipbubbleService->fetchRates($sender, $receiver, $package);

        if ($result && isset($result['status']) && $result['status'] === 'success') {
            return response()->json([
                'success' => true,
                'rates' => $result['data']['rates'] ?? []
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to calculate rates from Shipbubble at this time.'
        ], 400);
    }
}
