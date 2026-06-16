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
        $hasExistingProfile = $this->hasExistingProfile($user);

        $fileRule = $hasExistingProfile ? 'nullable|file' : 'required|file';

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
            'cac_document' => $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120',
            'valid_id' => $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_of_address' => $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        if ($user->hasRole('exporter')) {
            $rules['trade_capacity'] = 'nullable|string|max:255';
            $rules['nepc_certificate'] = $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120';
        } elseif ($user->hasRole('buyer')) {
            $rules['trade_capacity'] = 'nullable|string|max:255';
        } elseif ($user->hasRole('logistics')) {
            $rules['fleet_size'] = 'nullable|string|max:255';
            $rules['git_insurance'] = $fileRule . '|mimes:pdf,jpg,jpeg,png|max:5120';
        } elseif ($user->hasRole('field-officer')) {
            $rules['identification_number'] = 'nullable|string|max:255';
            $rules['full_name'] = $textRule . '|max:255';
            $rules['phone'] = $textRule . '|max:20';
        }

        return $rules;
    }

    private function hasExistingProfile($user): bool
    {
        if ($user->hasRole('exporter')) {
            return \App\Models\ExporterProfile::where('user_id', $user->id)->exists();
        }
        if ($user->hasRole('buyer')) {
            return \App\Models\BuyerProfile::where('user_id', $user->id)->exists();
        }
        if ($user->hasRole('logistics')) {
            return \App\Models\LogisticsProfile::where('user_id', $user->id)->exists();
        }
        if ($user->hasRole('field-officer')) {
            return \App\Models\FieldOfficerProfile::where('user_id', $user->id)->exists();
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
            'nepc_certificate.required' => 'NEPC Certificate is required for exporters.',
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
