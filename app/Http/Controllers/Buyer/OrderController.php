<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\BuyerProfile;
use App\Models\SellerProfile;
use App\Models\Settlement;
use App\Models\Shipment;
use App\Models\AtexAuditLog;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $orders = Order::with(['buyerProfile', 'sellerProfile', 'product'])->latest()->get();
            return view('buyer.orders.admin', compact('orders'));
        }

        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            $orders = Order::where('seller_profile_id', $profile->id ?? 0)->with(['buyerProfile', 'product'])->latest()->get();
            return view('buyer.orders.seller', compact('orders'));
        }

        if ($user->hasRole('buyer')) {
            $profile = BuyerProfile::where('user_id', $user->id)->first();
            $orders = Order::where('buyer_profile_id', $profile->id ?? 0)->with(['sellerProfile', 'product'])->latest()->get();
            return view('buyer.orders.buyer', compact('orders'));
        }

        if ($user->hasRole('logistics')) {
            $profile = LogisticsProfile::where('user_id', $user->id)->first();
            $profileId = $profile->id ?? 0;
            $orders = Order::whereHas('shipment', function ($query) use ($profileId) {
                $query->where('logistics_profile_id', $profileId);
            })->with(['buyerProfile', 'sellerProfile', 'product', 'shipment'])->latest()->get();
            return view('buyer.orders.logistics', compact('orders'));
        }

        return redirect()->route('admin.dashboard');
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('buyer')) {
            return redirect()->route('admin.dashboard')->with('error', 'Only buyers can create orders.');
        }

        $productId = $request->product_id;
        $product = Product::with('sellerProfile')->findOrFail($productId);
        
        return view('buyer.orders.create', compact('product'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('buyer')) {
            return redirect()->route('admin.dashboard')->with('error', 'Only buyers can place orders.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_quantity' => 'required|string|max:100',
            'destination_location' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:1',
            'currency' => 'required|string|max:10',
        ]);

        $product = Product::findOrFail($request->product_id);
        $buyer = BuyerProfile::where('user_id', $user->id)->first();
        if (!$buyer) {
            $buyer = BuyerProfile::create([
                'user_id' => $user->id,
                'country' => 'Nigeria',
                'verification_status' => 'approved',
            ]);
        }

        $orderNumber = 'AEM-ORD-' . strtoupper(Str::random(6));
        $totalAmount = $request->total_amount;
        
        // Compute commission (10%), tax (7.5%), net payout
        $commission = round($totalAmount * 0.10, 2);
        $tax = round($totalAmount * 0.075, 2);
        $netPayout = round($totalAmount - $commission - $tax, 2);

        $order = Order::create([
            'order_number' => $orderNumber,
            'product_id' => $product->id,
            'buyer_profile_id' => $buyer->id,
            'seller_profile_id' => $product->seller_profile_id,
            'order_quantity' => $request->order_quantity,
            'destination_location' => $request->destination_location,
            'total_amount' => $totalAmount,
            'currency' => $request->currency,
            'fulfillment_mode' => $product->fulfillment_mode,
            'fulfillment_status' => 'pending',
            'commission_amount' => $commission,
            'tax_amount' => $tax,
            'net_payout_amount' => $netPayout,
            'settlement_status' => 'pending',
            'payment_status' => 'held',
            'shipment_status' => 'pending_assignment',
            'status' => 'created',
        ]);

        // Create Settlement Record
        Settlement::create([
            'order_id' => $order->id,
            'seller_profile_id' => $product->seller_profile_id,
            'gross_amount' => $totalAmount,
            'commission_amount' => $commission,
            'tax_amount' => $tax,
            'net_payout_amount' => $netPayout,
            'status' => 'pending',
        ]);

        // Create Shipment Record
        Shipment::create([
            'order_id' => $order->id,
            'status' => 'pending_assignment',
            'origin_location' => ($product->origin_lga ?: 'Yola') . ', Adamawa',
            'destination_location' => $request->destination_location,
        ]);

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'placed_order',
            'auditable_type' => 'order',
            'auditable_id' => $order->id,
            'new_values' => json_encode(['order_number' => $orderNumber]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.orders.index')->with('success', 'Order placed successfully. Payment is currently in escrow hold.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $order = Order::with(['buyerProfile.user', 'sellerProfile.user', 'product', 'settlement', 'shipment.logisticsProfile.user'])->findOrFail($id);

        // Security check
        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            if ($order->seller_profile_id !== $profile->id) {
                abort(403);
            }
        } elseif ($user->hasRole('buyer')) {
            $profile = BuyerProfile::where('user_id', $user->id)->first();
            if ($order->buyer_profile_id !== $profile->id) {
                abort(403);
            }
        } elseif ($user->hasRole('logistics')) {
            $profile = LogisticsProfile::where('user_id', $user->id)->first();
            if ($order->shipment && $order->shipment->logistics_profile_id !== $profile->id) {
                abort(403);
            }
        }

        return view('buyer.orders.show', compact('order', 'user'));
    }

    public function track($reference)
    {
        $order = Order::where('order_number', $reference)->with(['shipment', 'product', 'sellerProfile', 'buyerProfile'])->first();

        if (!$order) {
            // Dummy tracking data for dashboard sample orders
            $tracking = [
                'order_number' => $reference,
                'status' => match (true) {
                    str_contains($reference, '12345') => 'Processing',
                    str_contains($reference, '12344') => 'Delivered',
                    default => 'Shipped',
                },
                'payment_status' => 'held',
                'shipment_status' => match (true) {
                    str_contains($reference, '12345') => 'picked_up',
                    str_contains($reference, '12344') => 'delivered',
                    default => 'in_transit',
                },
                'product_name' => 'Premium Export Product',
                'quantity' => '50 MT',
                'origin' => 'Yola, Adamawa',
                'destination' => 'Lagos Port',
                'tracking_number' => 'AEM-' . strtoupper(substr(md5($reference), 0, 8)),
                'logistics_partner' => 'Sahel Freight Logistics',
                'created_at' => now()->subDays(match (true) {
                    str_contains($reference, '12345') => 2,
                    str_contains($reference, '12344') => 14,
                    default => 7,
                }),
                'timeline' => [
                    ['status' => 'Order Placed', 'date' => now()->subDays(7), 'description' => 'Quote accepted and order created'],
                    ['status' => 'Payment Confirmed', 'date' => now()->subDays(6), 'description' => 'Payment held in escrow'],
                    ['status' => 'Processing', 'date' => now()->subDays(4), 'description' => 'Seller preparing shipment'],
                    ['status' => 'Picked Up', 'date' => now()->subDays(2), 'description' => 'Cargo picked up by logistics partner'],
                    ['status' => 'In Transit', 'date' => now()->subDay(), 'description' => 'Shipment en route to destination'],
                ],
            ];
            return view('buyer.orders.track', compact('tracking'));
        }

        $tracking = [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'shipment_status' => $order->shipment_status,
            'product_name' => $order->product->name ?? 'Direct Trade Lot',
            'quantity' => $order->order_quantity,
            'origin' => $order->shipment->origin_location ?? 'Adamawa',
            'destination' => $order->destination_location,
            'tracking_number' => $order->shipment->tracking_number ?? 'Not assigned',
            'logistics_partner' => $order->shipment->logisticsProfile->company_name ?? 'TBD',
            'created_at' => $order->created_at,
            'timeline' => [],
        ];

        $shipStatus = $order->shipment_status;
        $tracking['timeline'][] = ['status' => 'Order Placed', 'date' => $order->created_at, 'description' => 'Quote accepted and order created'];
        $tracking['timeline'][] = ['status' => 'Payment Confirmed', 'date' => $order->created_at->addDay(), 'description' => 'Payment held in escrow'];
        if (in_array($shipStatus, ['picked_up', 'customs_cleared', 'departed_origin', 'in_transit', 'delivered'])) {
            $tracking['timeline'][] = ['status' => 'Picked Up', 'date' => $order->created_at->addDays(3), 'description' => 'Cargo picked up by logistics'];
        }
        if (in_array($shipStatus, ['customs_cleared', 'departed_origin', 'in_transit', 'delivered'])) {
            $tracking['timeline'][] = ['status' => 'Customs Cleared', 'date' => $order->created_at->addDays(5), 'description' => 'Export documentation approved'];
        }
        if (in_array($shipStatus, ['departed_origin', 'in_transit', 'delivered'])) {
            $tracking['timeline'][] = ['status' => 'Departed Origin', 'date' => $order->created_at->addDays(7), 'description' => 'Shipment departed origin'];
        }
        if (in_array($shipStatus, ['in_transit', 'delivered'])) {
            $tracking['timeline'][] = ['status' => 'In Transit', 'date' => $order->created_at->addDays(10), 'description' => 'Shipment en route'];
        }
        if ($shipStatus === 'delivered') {
            $tracking['timeline'][] = ['status' => 'Delivered', 'date' => $order->created_at->addDays(14), 'description' => 'Shipment delivered successfully'];
        }

        return view('buyer.orders.track', compact('tracking'));
    }

    public function fulfillment()
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $orders = Order::where('fulfillment_mode', 'afribidge')
            ->with(['buyerProfile', 'sellerProfile', 'product', 'shipment.logisticsProfile'])
            ->latest()
            ->get();
        $logistics = LogisticsProfile::where('verification_status', 'approved')->get();

        return view('buyer.fulfillment.index', compact('orders', 'logistics'));
    }

    public function fulfillmentUpdate(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'fulfillment_status' => 'required|string|max:30',
            'shipment_status' => 'required|string|max:30',
            'status' => 'required|string|max:30',
        ]);

        $order = Order::findOrFail($id);
        $oldFulfillmentStatus = $order->fulfillment_status;

        $order->update([
            'fulfillment_status' => $request->fulfillment_status,
            'shipment_status' => $request->shipment_status,
            'status' => $request->status,
        ]);

        // Sync to shipment status
        if ($order->shipment) {
            $order->shipment->update([
                'status' => $request->shipment_status,
            ]);
        }

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'updated_fulfillment_order',
            'auditable_type' => 'order',
            'auditable_id' => $order->id,
            'old_values' => json_encode(['fulfillment_status' => $oldFulfillmentStatus]),
            'new_values' => json_encode([
                'fulfillment_status' => $request->fulfillment_status,
                'shipment_status' => $request->shipment_status,
                'status' => $request->status,
            ]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.fulfillment.index')->with('success', 'Fulfillment order status updated successfully.');
    }

    public function review($reference)
    {
        $order = Order::where('order_number', $reference)->first();
        if (!$order) {
            return view('buyer.orders.review', [
                'reference' => $reference,
                'product_name' => 'Premium Export Product',
                'order' => null,
            ]);
        }
        return view('buyer.orders.review', [
            'reference' => $reference,
            'product_name' => $order->product->name ?? 'Direct Trade Lot',
            'order' => $order,
        ]);
    }

    public function storeReview(Request $request, $reference)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $existing = ProductReview::where('user_id', Auth::id())
            ->where('order_reference', $reference)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this order.');
        }

        ProductReview::create([
            'user_id' => Auth::id(),
            'order_reference' => $reference,
            'product_name' => $request->product_name ?? 'Premium Export Product',
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('buyer.dashboard')->with('success', 'Thank you! Your review has been submitted.');
    }

    public function reorder($reference)
    {
        $order = Order::where('order_number', $reference)->first();
        if (!$order) {
            return view('buyer.orders.reorder', [
                'reference' => $reference,
                'product_name' => 'Premium Export Product',
                'amount' => match ($reference) {
                    'ORD-12343' => '15000',
                    default => '0',
                },
                'order' => null,
            ]);
        }
        return view('buyer.orders.reorder', [
            'reference' => $reference,
            'product_name' => $order->product->name ?? 'Direct Trade Lot',
            'amount' => $order->total_amount,
            'order' => $order,
        ]);
    }
}

