<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\KycRequest;
use App\Models\SellerProfile;
use App\Models\BuyerProfile;
use App\Models\LogisticsProfile;
use App\Models\AdminProfile;
use App\Models\ExporterProfile;
use App\Models\Document;
use App\Events\KycSubmitted;
use Illuminate\Support\Facades\Auth;

class KycOnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        $profile = $this->getProfile($user);

        if ($profile && $profile->verification_status === 'approved') {
            return redirect()->route('dashboard');
        }

        return view('auth.kyc', compact('profile'));
    }

    public function store(KycRequest $request)
    {
        $user = Auth::user();

        if ($user->hasRole('seller') || $user->hasRole('logistics') || $user->hasRole('admin')) {
            // Fall through to the detailed KYC flow below for these roles
        } elseif ($user->hasRole('buyer')) {
            $profile = BuyerProfile::updateOrCreate(
                ['user_id' => $user->id],
                array_merge(
                    $request->only(['phone_number', 'gender', 'shipping_address', 'billing_address', 'city', 'state', 'zip_code', 'country']),
                    ['verification_status' => 'approved', 'approved_at' => now()]
                )
            );


            return redirect()->route('dashboard')->with('success', 'Profile completed successfully.');
        }

        $existingProfile = $this->getProfile($user);

        $data = ['verification_status' => 'pending'];

        // Clear only rejected statuses on resubmit (keep approved ones)
        if ($existingProfile && $existingProfile->regulatory_reviews) {
            $reviews = $existingProfile->regulatory_reviews;
            foreach ($reviews as $field => $review) {
                if (isset($review['status']) && $review['status'] === 'rejected') {
                    unset($reviews[$field]);
                }
            }
            $data['regulatory_reviews'] = $reviews;
            $data['rejection_reason'] = null;
        }

        // Include text fields when submitted (new profile or existing correcting rejected fields)
        $textFields = [
            'registration_number', 'tax_number', 'bvn', 'nin', 'address',
            'country', 'state',
            'bank_name', 'account_number', 'account_name',
        ];
        foreach ($textFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->$field;
            }
        }

        $profile = null;
        $profileType = '';

        if ($user->hasRole('seller')) {
            if (!$existingProfile || $request->filled('business_name')) {
                $data['business_name'] = $request->business_name;
            }
            if ($request->filled('seller_tier')) {
                $data['seller_tier'] = $request->seller_tier;
            } elseif ($request->hasFile('nepc_certificate')) {
                $data['seller_tier'] = 'export';
            }
            $isExporter = ($existingProfile && $existingProfile->seller_tier === 'export') || ($data['seller_tier'] ?? null) === 'export';
            $exportData = [];
            if ($isExporter) {
                if ($request->filled('trade_capacity')) {
                    $exportData['export_capacity'] = $request->trade_capacity;
                }
                if ($request->filled('export_markets')) {
                    $exportData['export_markets'] = $request->export_markets;
                }
            }
            $profile = SellerProfile::updateOrCreate(['user_id' => $user->id], $data);
            if ($isExporter) {
                ExporterProfile::updateOrCreate(
                    ['seller_profile_id' => $profile->id],
                    array_merge(['verification_status' => 'pending'], $exportData)
                );
            }
            $profileType = 'seller';
        } elseif ($user->hasRole('logistics')) {
            if (!$existingProfile || $request->filled('business_name')) {
                $data['company_name'] = $request->business_name;
            }
            if (!$existingProfile || $request->filled('fleet_size')) {
                $data['fleet_size'] = $request->fleet_size;
            }
            $profile = LogisticsProfile::updateOrCreate(['user_id' => $user->id], $data);
            $profileType = 'logistics';
        } elseif ($user->hasRole('admin')) {
            if (!$existingProfile || $request->filled('full_name')) {
                $data['full_name'] = $request->full_name;
            }
            if (!$existingProfile || $request->filled('phone')) {
                $data['phone'] = $request->phone;
            }
            if (!$existingProfile || $request->filled('identification_number')) {
                $data['identification_number'] = $request->identification_number;
            }
            $profile = AdminProfile::updateOrCreate(['user_id' => $user->id], $data);
            $profileType = 'admin';
        }

        if ($profile && $profileType) {
            $this->uploadDocument($request, 'cac_document', 'CAC Certificate', $profileType, $profile->id);
            $this->uploadDocument($request, 'valid_id', 'Valid Identification', $profileType, $profile->id);
            $this->uploadDocument($request, 'proof_of_address', 'Proof of Address', $profileType, $profile->id);

            if ($user->hasRole('seller') && ($profile->seller_tier === 'export' || $request->hasFile('nepc_certificate'))) {
                $this->uploadDocument($request, 'nepc_certificate', 'NEPC Export Certificate', $profileType, $profile->id);
            }
            if ($user->hasRole('logistics')) {
                $this->uploadDocument($request, 'git_insurance', 'Goods in Transit Insurance', $profileType, $profile->id);
            }


            event(new KycSubmitted($user, $profileType, $profile));
        }

        return redirect()->route('kyc.onboarding')->with('success', 'KYC application submitted successfully. Please wait for admin approval.');
    }

    private function getProfile($user)
    {
        if ($user->hasRole('seller')) {
            return SellerProfile::where('user_id', $user->id)->first();
        }
        if ($user->hasRole('buyer')) {
            return BuyerProfile::where('user_id', $user->id)->first();
        }
        if ($user->hasRole('logistics')) {
            return LogisticsProfile::where('user_id', $user->id)->first();
        }
        if ($user->hasRole('admin')) {
            return AdminProfile::where('user_id', $user->id)->first();
        }
        return null;
    }

    private function uploadDocument($request, $fieldName, $title, $ownerType, $ownerId): void
    {
        if ($request->hasFile($fieldName)) {
            $existing = Document::where('owner_type', $ownerType)
                ->where('owner_id', $ownerId)
                ->where('document_type', $fieldName)
                ->first();

            if ($existing && $existing->status === 'approved') {
                return;
            }

            $path = $request->file($fieldName)->store('documents/kyc', 'public');
            Document::updateOrCreate(
                [
                    'owner_type' => $ownerType,
                    'owner_id' => $ownerId,
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
