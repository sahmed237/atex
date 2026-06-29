<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\BuyerProfile;
use App\Models\LogisticsProfile;
use App\Models\AdminProfile;
use App\Models\Product;
use App\Models\Document;
use App\Models\QuoteRequest;
use App\Models\Order;
use App\Models\FulfillmentInventory;
use App\Models\Settlement;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $user = Auth::user();
        $metrics = [
            'users' => User::count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'sellers' => SellerProfile::count(),
            'buyers' => BuyerProfile::count(),
            'pending_kyc' => SellerProfile::whereIn('verification_status', ['pending', 'submitted'])->count()
                + LogisticsProfile::whereIn('verification_status', ['pending', 'submitted'])->count()
                + AdminProfile::whereIn('verification_status', ['pending', 'submitted'])->count(),
            'products' => Product::count(),
            'pending_documents' => Document::where('status', 'pending')->count(),
            'open_quotes' => QuoteRequest::where('status', 'open')->count(),
            'orders' => Order::count(),
            'inventory_items' => FulfillmentInventory::count(),
            'pending_settlements' => Settlement::whereIn('status', ['pending', 'processing'])->count(),
            'logistics_partners' => LogisticsProfile::count(),
            'export_value' => Order::sum('total_amount'),
        ];

        return view('admin.dashboard.admin', compact('metrics', 'user'));
    }



    public function logisticsDashboard()
    {
        $user = Auth::user();
        $profile = LogisticsProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = LogisticsProfile::create([
                'user_id' => $user->id,
                'company_name' => $user->name,
                'verification_status' => 'pending',
            ]);
        }

        $metrics = [
            'active_shipments' => Shipment::where('logistics_profile_id', $profile->id)
                ->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'completed_shipments' => Shipment::where('logistics_profile_id', $profile->id)
                ->where('status', 'delivered')->count(),
            'pending_payouts' => Settlement::where('logistics_profile_id', $profile->id)
                ->whereIn('status', ['pending', 'processing'])->sum('net_payout_amount'),
            'total_earnings' => Settlement::where('logistics_profile_id', $profile->id)
                ->where('status', 'credited')->sum('net_payout_amount'),
        ];

        return view('admin.dashboard.logistics', compact('metrics', 'profile', 'user'));
    }

    public function buyerDashboard()
    {
        $user = Auth::user();
        $buyerProfile = \App\Models\BuyerProfile::where('user_id', $user->id)->first();
        
        $totalOrders = 0;
        $totalSpent = 0.00;
        $recentOrders = collect();

        if ($buyerProfile) {
            $totalOrders = \App\Models\Order::where('buyer_profile_id', $buyerProfile->id)->count();
            $totalSpent = \App\Models\Order::where('buyer_profile_id', $buyerProfile->id)->sum('total_amount');
            $recentOrders = \App\Models\Order::where('buyer_profile_id', $buyerProfile->id)->with('product')->latest()->take(5)->get();
        }

        $metrics = [
            'total_orders' => $totalOrders,
            'active_rfqs' => 0,
            'saved_items' => 0,
            'total_spent' => $totalSpent,
        ];
        $sellerProfile = \App\Models\SellerProfile::where('user_id', $user->id)->first();
        return view('admin.dashboard.buyer', compact('metrics', 'user', 'sellerProfile', 'recentOrders'));
    }
}
