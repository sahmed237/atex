<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Models\LogisticsProfile;
use App\Models\AtexAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            return view('atex.profile.seller', compact('profile', 'user'));
        }

        if ($user->hasRole('logistics')) {
            $profile = LogisticsProfile::where('user_id', $user->id)->first();
            return view('atex.profile.logistics', compact('profile', 'user'));
        }

        return redirect()->route('admin.dashboard');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('seller')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'business_name' => 'required|string|max:255',
                'registration_number' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:255',
                'business_type' => 'required|string|max:100',
                'lga' => 'required|string|max:255',
                'address' => 'nullable|string',
                'seller_brand_name' => 'nullable|string|max:255',
                'fulfillment_model' => 'required|in:seller_direct,afribidge',
            ]);

            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);

            $profile = SellerProfile::where('user_id', $user->id)->first();
            $profile->update([
                'business_name' => $request->business_name,
                'registration_number' => $request->registration_number,
                'tax_number' => $request->tax_number,
                'business_type' => $request->business_type,
                'lga' => $request->lga,
                'address' => $request->address,
                'seller_brand_name' => $request->seller_brand_name ?: $request->business_name,
                'fulfillment_model' => $request->fulfillment_model,
            ]);

            AtexAuditLog::create([
                'actor_id' => $user->id,
                'action' => 'updated_seller_profile',
                'auditable_type' => 'seller_profile',
                'auditable_id' => $profile->id,
                'new_values' => json_encode(['business_name' => $request->business_name]),
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully.');
        }

        if ($user->hasRole('logistics')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:50',
                'company_name' => 'required|string|max:255',
                'coverage_regions' => 'nullable|string',
                'transport_modes' => 'nullable|string|max:255',
                'base_location' => 'nullable|string|max:255',
                'fleet_capacity' => 'nullable|string|max:255',
            ]);

            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
            ]);

            $profile = LogisticsProfile::where('user_id', $user->id)->first();
            $profile->update([
                'company_name' => $request->company_name,
                'coverage_regions' => $request->coverage_regions,
                'transport_modes' => $request->transport_modes,
                'base_location' => $request->base_location,
                'fleet_capacity' => $request->fleet_capacity,
            ]);

            AtexAuditLog::create([
                'actor_id' => $user->id,
                'action' => 'updated_logistics_profile',
                'auditable_type' => 'logistics_profile',
                'auditable_id' => $profile->id,
                'new_values' => json_encode(['company_name' => $request->company_name]),
                'ip_address' => $request->ip(),
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully.');
        }

        return redirect()->route('admin.dashboard');
    }
}

