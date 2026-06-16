<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ExporterProfile;
use App\Models\BuyerProfile;
use App\Models\LogisticsProfile;
use App\Models\FieldOfficerProfile;
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
            'exporters' => ExporterProfile::count(),
            'buyers' => BuyerProfile::count(),
            'pending_kyc' => ExporterProfile::whereIn('verification_status', ['pending', 'submitted'])->count()
                + BuyerProfile::whereIn('verification_status', ['pending', 'submitted'])->count()
                + LogisticsProfile::whereIn('verification_status', ['pending', 'submitted'])->count()
                + FieldOfficerProfile::whereIn('verification_status', ['pending', 'submitted'])->count(),
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

    public function exporterDashboard()
    {
        $user = Auth::user();
        $profile = ExporterProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            // Create profile dynamically if missing
            $profile = ExporterProfile::create([
                'user_id' => $user->id,
                'business_name' => $user->name,
                'lga' => 'Yola North',
                'verification_status' => 'pending',
            ]);
        }

        $profileId = $profile->id;

        $metrics = [
            'products' => Product::where('exporter_profile_id', $profileId)->count(),
            'pending_products' => Product::where('exporter_profile_id', $profileId)->where('status', 'pending_review')->count(),
            'documents' => Document::where('owner_type', 'exporter')->where('owner_id', $profileId)->count(),
            'pending_documents' => Document::where('owner_type', 'exporter')->where('owner_id', $profileId)->where('status', 'pending')->count(),
            'quotes' => QuoteRequest::whereHas('product', function ($query) use ($profileId) {
                $query->where('exporter_profile_id', $profileId);
            })->count(),
            'orders' => Order::where('exporter_profile_id', $profileId)->count(),
            'inventory_items' => FulfillmentInventory::where('exporter_profile_id', $profileId)->count(),
            'payouts' => Settlement::where('exporter_profile_id', $profileId)->count(),
            'pending_payout' => Settlement::where('exporter_profile_id', $profileId)->whereIn('status', ['pending', 'processing'])->sum('net_payout_amount'),
            'credited_payout' => Settlement::where('exporter_profile_id', $profileId)->where('status', 'credited')->sum('net_payout_amount'),
            'export_value' => Order::where('exporter_profile_id', $profileId)->sum('total_amount'),
            'readiness' => $profile->readiness_score,
        ];

        return view('admin.dashboard.exporter', compact('metrics', 'profile', 'user'));
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
        return redirect('/#marketplace');
    }
}
