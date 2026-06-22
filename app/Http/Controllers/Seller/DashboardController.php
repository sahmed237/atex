<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\Document;
use App\Models\QuoteRequest;
use App\Models\Order;
use App\Models\FulfillmentInventory;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = SellerProfile::where('user_id', $user->id)->first();
        
        if (!$profile) {
            // Create profile dynamically if missing
            $profile = SellerProfile::create([
                'user_id' => $user->id,
                'business_name' => $user->name,
                'lga' => 'Yola North',
                'verification_status' => 'pending',
            ]);
        }

        $profileId = $profile->id;

        $metrics = [
            'products' => Product::where('seller_profile_id', $profileId)->count(),
            'pending_products' => Product::where('seller_profile_id', $profileId)->where('status', 'pending_review')->count(),
            'documents' => Document::where('owner_type', 'seller')->where('owner_id', $profileId)->count(),
            'pending_documents' => Document::where('owner_type', 'seller')->where('owner_id', $profileId)->where('status', 'pending')->count(),
            'quotes' => QuoteRequest::whereHas('product', function ($query) use ($profileId) {
                $query->where('seller_profile_id', $profileId);
            })->count(),
            'orders' => Order::where('seller_profile_id', $profileId)->count(),
            'inventory_items' => FulfillmentInventory::where('seller_profile_id', $profileId)->count(),
            'payouts' => Settlement::where('seller_profile_id', $profileId)->count(),
            'pending_payout' => Settlement::where('seller_profile_id', $profileId)->whereIn('status', ['pending', 'processing'])->sum('net_payout_amount'),
            'credited_payout' => Settlement::where('seller_profile_id', $profileId)->where('status', 'credited')->sum('net_payout_amount'),
            'export_value' => Order::where('seller_profile_id', $profileId)->sum('total_amount'),
            'readiness' => $profile->readiness_score,
        ];

        return view('seller.dashboard', compact('metrics', 'profile', 'user'));
    }
}

