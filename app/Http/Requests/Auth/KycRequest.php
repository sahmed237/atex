<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class KycRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $user = Auth::user();

        if ($user->hasRole('seller')) {
            // we let it fall through to the seller rules below
        } elseif ($user->hasRole('logistics')) {
            // let it fall through
        } elseif ($user->hasRole('admin')) {
            // let it fall through
        } elseif ($user->hasRole('buyer')) {
            return [
                'phone_number' => 'required|string|max:20',
                'shipping_address' => 'required|string',
                'billing_address' => 'nullable|string',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'zip_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
            ];
        }

        $hasExistingProfile = $this->hasExistingProfile($user);

        $fileRule = $hasExistingProfile ? 'nullable|file' : 'required|file';
        $optionalFile = 'nullable|file';

        $textRule = $hasExistingProfile ? 'nullable|string' : 'required|string';

        $rules = [
            'business_name' => $textRule . '|max:255',
            'registration_number' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:255',
            'bvn' => $textRule . '|size:11',
            'nin' => $textRule . '|size:11',
            'address' => $textRule,
            'bank_name' => $textRule . '|max:255',
            'account_number' => $textRule . '|max:20',
            'account_name' => $textRule . '|max:255',
            'valid_id' => $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_of_address' => $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        if ($user->hasRole('seller')) {
            // CAC registration is an export-compliance requirement; optional for local sellers.
            $isExporter = $this->sellerTier($user) === 'export';
            $rules['cac_document'] = $isExporter
                ? $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120'
                : $optionalFile . '|mimes:pdf,jpg,jpeg,png|max:5120';
            if ($isExporter) {
                $rules['nepc_certificate'] = $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120';
                $rules['trade_capacity'] = 'nullable|string|max:255';
                $rules['export_markets'] = 'nullable|string|max:255';
            }
        } elseif ($user->hasRole('logistics')) {
            $rules['cac_document'] = $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120';
            $rules['fleet_size'] = 'nullable|string|max:255';
            $rules['git_insurance'] = $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120';
        } elseif ($user->hasRole('admin')) {
            $rules['cac_document'] = $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120';
            $rules['identification_number'] = 'nullable|string|max:255';
            $rules['full_name'] = $textRule . '|max:255';
            $rules['phone'] = $textRule . '|max:20';
        }

        return $rules;
    }

    private function sellerTier($user): ?string
    {
        return \App\Models\SellerProfile::where('user_id', $user->id)->value('seller_tier');
    }

    private function hasExistingProfile($user): bool
    {
        if ($user->hasRole('seller')) {
            return \App\Models\SellerProfile::where('user_id', $user->id)->exists();
        }
        if ($user->hasRole('buyer')) {
            return \App\Models\BuyerProfile::where('user_id', $user->id)->exists();
        }
        if ($user->hasRole('logistics')) {
            return \App\Models\LogisticsProfile::where('user_id', $user->id)->exists();
        }
        if ($user->hasRole('admin')) {
            return \App\Models\AdminProfile::where('user_id', $user->id)->exists();
        }
        return false;
    }

    public function messages(): array
    {
        return [
            'bvn.required' => 'Bank Verification Number is required.',
            'bvn.size' => 'BVN must be exactly 11 digits.',
            'nin.required' => 'National Identity Number is required.',
            'nin.size' => 'NIN must be exactly 11 digits.',
            'nepc_certificate.required' => 'NEPC Certificate is required for sellers.',
            'git_insurance.required' => 'Goods in Transit Insurance is required for logistics partners.',
            'cac_document.required' => 'CAC Certificate is required.',
            'cac_document.file' => 'CAC Certificate must be a valid file.',
            'valid_id.required' => 'A valid government-issued ID is required.',
            'valid_id.file' => 'Valid ID must be a valid file.',
            'proof_of_address.required' => 'Proof of address (utility bill) is required.',
            'proof_of_address.file' => 'Proof of address must be a valid file.',
        ];
    }
}
