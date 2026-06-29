<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SellerProfile;
use App\Models\SellerProfileKyc;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class SellerOnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $profile = SellerProfile::where('user_id', $user->id)->first();

        $rejectedFields = collect();
        $documents = collect();

        if ($profile) {
            if ($profile->verification_status === 'pending') {
                return view('seller.onboarding.pending', compact('profile'));
            }

            if ($profile->seller_tier === 'local' && $profile->verification_status === 'approved') {
                return redirect()->route('exporter.onboarding');
            }

            if ($profile->verification_status === 'rejected') {
                $rejectedFields = \App\Models\SellerProfileKycItemReview::where('owner_type', 'seller')
                    ->where('owner_id', $profile->id)
                    ->where('status', 'rejected')
                    ->get()
                    ->keyBy('item_key');
                
                $documents = \App\Models\Document::where('owner_type', 'seller')
                    ->where('owner_id', $profile->id)
                    ->get()
                    ->keyBy('document_type');
            } else {
                return redirect()->route('dashboard');
            }
        }

        $categories = \App\Models\BusinessCategory::where('status', true)->orderBy('name')->get();
        return view('seller.onboarding.index', compact('categories', 'profile', 'rejectedFields', 'documents'));
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
            'seller_brand_name' => 'nullable|string|max:255',
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:255',
            'residential_address' => 'required|string',
            'id_type' => 'required|string|in:nin,passport,drivers,voter',
            'id_number' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        
        $sellerProfile = SellerProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => $request->business_name,
                'seller_brand_name' => $request->seller_brand_name,
                'business_description' => $request->business_description,
                'business_category' => $request->business_category,
                'country' => $request->country,
                'state' => $request->state,
                'lga' => $request->lga,
                'city' => $request->city,
                'address' => $request->business_address,
                'phone' => $request->phone,
                'seller_tier' => 'local',
                'verification_status' => 'pending',
                'seller_program_status' => 'pending',
                'readiness_score' => 40,
                'fulfillment_model' => 'seller_direct',
            ]
        );

        $kycData = [
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'nationality' => $request->nationality,
            'residential_address' => $request->residential_address,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
        ];

        $sellerProfile->kyc()->updateOrCreate(
            ['seller_profile_id' => $sellerProfile->id],
            $kycData
        );

        if (!$user->hasRole('seller')) {
            $user->assignRole('seller');
        }

        $this->uploadDocument($request, 'cac_document', 'CAC Certificate', $sellerProfile->id);
        $this->uploadDocument($request, 'valid_id', 'Valid Identification', $sellerProfile->id);
        $this->uploadDocument($request, 'proof_of_address', 'Proof of Address', $sellerProfile->id);

        return redirect()->route('dashboard')->with('status', 'Your seller registration has been submitted for review.');
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
