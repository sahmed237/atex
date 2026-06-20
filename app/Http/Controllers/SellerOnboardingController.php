<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerProfile;
use App\Models\Document;

class SellerOnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            if (!$profile || $profile->verification_status === 'pending') {
                return redirect()->route('kyc.onboarding');
            }
            if ($profile->seller_tier === 'local' && $profile->verification_status === 'approved') {
                return redirect()->route('seller.onboarding.upgrade');
            }
            return redirect()->route('dashboard');
        }
        return view('seller.onboarding.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'business_category' => 'required|string|max:255',
            'business_address' => 'required|string',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'lga' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'nin' => 'required|string|max:11',
        ]);

        $user = Auth::user();
        $user->assignRole('seller');

        SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => $request->business_name,
                'business_description' => $request->business_description,
                'business_category' => $request->business_category,
                'country' => $request->country,
                'state' => $request->state,
                'lga' => $request->lga,
                'city' => $request->city,
                'address' => $request->business_address,
                'phone' => $request->phone,
                'nin' => $request->nin,
                'seller_tier' => 'local',
                'verification_status' => 'pending',
                'seller_program_status' => 'pending',
                'readiness_score' => 40,
                'fulfillment_model' => 'seller_direct',
            ]
        );

        $user->kyc_verification_status = 'pending';
        $user->kyc_submitted_at = now();
        $user->save();

        return redirect()->route('kyc.onboarding')->with('success', 'Your local seller registration has been submitted for review. You will be notified once approved.');
    }

    public function showUpgrade()
    {
        $user = Auth::user();
        if (!$user->hasRole('seller')) {
            return redirect()->route('seller.onboarding');
        }

        $profile = SellerProfile::where('user_id', $user->id)->first();
        if (!$profile || $profile->seller_tier === 'export') {
            return redirect()->route('dashboard');
        }

        if ($profile->verification_status !== 'approved') {
            return redirect()->route('kyc.onboarding');
        }

        return view('seller.onboarding.upgrade', compact('profile'));
    }

    public function storeUpgrade(Request $request)
    {
        $request->validate([
            'registration_number' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'bvn' => 'required|string|max:11',
            'nin' => 'required|string|max:11',
            'seller_brand_name' => 'nullable|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:10',
            'account_name' => 'required|string|max:255',
            'trade_capacity' => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer|min:0',
            'export_markets' => 'nullable|string|max:255',
            'document_cac' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_logo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'document_id' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'document_address' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $user = Auth::user();
        $profile = SellerProfile::where('user_id', $user->id)->firstOrFail();

        $profile->update([
            'registration_number' => $request->registration_number,
            'tax_number' => $request->tax_number,
            'bvn' => $request->bvn,
            'nin' => $request->nin,
            'seller_brand_name' => $request->seller_brand_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'trade_capacity' => $request->trade_capacity,
            'years_of_experience' => $request->years_of_experience,
            'export_markets' => $request->export_markets,
            'seller_tier' => 'export',
            'verification_status' => 'pending',
            'seller_program_status' => 'pending',
            'readiness_score' => 80,
            'approved_at' => null,
        ]);

        $this->uploadDocument($request, 'document_cac', 'CAC Certificate', $profile->id);
        $this->uploadDocument($request, 'document_logo', 'Business Logo', $profile->id);
        $this->uploadDocument($request, 'document_id', 'Valid Identification', $profile->id);
        $this->uploadDocument($request, 'document_address', 'Proof of Business Address', $profile->id);

        $user->kyc_verification_status = 'pending';
        $user->kyc_submitted_at = now();
        $user->save();

        return redirect()->route('kyc.onboarding')->with('success', 'Your export seller upgrade has been submitted for verification. We will notify you once approved.');
    }

    private function uploadDocument($request, $fieldName, $title, $profileId): void
    {
        if ($request->hasFile($fieldName)) {
            $path = $request->file($fieldName)->store('documents/kyc', 'public');
            Document::updateOrCreate(
                [
                    'owner_type' => 'seller',
                    'owner_id' => $profileId,
                    'document_type' => $fieldName,
                ],
                [
                    'title' => $title,
                    'path' => $path,
                    'status' => 'pending',
                ]
            );
        }
    }
}
