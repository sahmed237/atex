@extends('layouts.buyer')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{}">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-[#0f1111]">Upgrade to Export Seller</h1>
        <p class="text-sm text-[#565959]">Complete additional verification to sell internationally on Adamawa Export Market.</p>
    </div>

    <div class="bg-[#f0f8f0] border border-[#007600] rounded-lg px-4 py-3 mb-4 flex items-start gap-3">
        <i data-lucide="info" class="w-5 h-5 text-[#007600] shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-medium text-[#0f1111]">Stage 2 of 2 — Export Upgrade</p>
            <p class="text-xs text-[#565959]">Your local seller profile is active. Complete this step to reach international buyers. Your application will be reviewed by our team.</p>
        </div>
    </div>

    <div class="bg-[#f0f2f2] rounded-lg border border-[#e7e7e7] p-4 mb-4 flex items-center gap-3">
        <i data-lucide="store" class="w-5 h-5 text-[#007185]"></i>
        <div>
            <p class="text-sm font-medium text-[#0f1111]">{{ $profile->business_name }}</p>
            <p class="text-xs text-[#565959]">{{ $profile->country }} &middot; {{ $profile->state }}</p>
        </div>
        <span class="ml-auto text-[10px] font-bold text-[#007600] bg-[#f0f8f0] px-2 py-0.5 rounded">Local Seller</span>
    </div>

    @if(session('error'))
        <div class="bg-[#fff5f0] border border-[#ff8f00] text-[#c45500] px-4 py-3 rounded-lg text-sm mb-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-[#fff5f0] border border-[#ff8f00] text-[#c45500] px-4 py-3 rounded-lg text-sm mb-4">
            <ul class="list-disc pl-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('seller.onboarding.upgrade.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="building" class="w-4 h-4 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">Business Registration</h2>
                    <p class="text-xs text-[#565959]">Official registration details.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Brand Name</label>
                        <input type="text" name="seller_brand_name" value="{{ old('seller_brand_name', $profile->seller_brand_name) }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Your brand name">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">RC Number (CAC)</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number', $profile->registration_number) }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="RC-12345">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">TIN</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $profile->tax_number) }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Tax Identification Number">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Years of Experience</label>
                        <input type="number" name="years_of_experience" value="{{ old('years_of_experience', $profile->years_of_experience) }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Years in business">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="id-card" class="w-4 h-4 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">Identity Verification</h2>
                    <p class="text-xs text-[#565959]">Required for export compliance.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">BVN <span class="text-[#c45500]">*</span></label>
                        <input type="text" name="bvn" value="{{ old('bvn') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="11-digit BVN">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">NIN <span class="text-[#c45500]">*</span></label>
                        <input type="text" name="nin" value="{{ old('nin') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="National Identification Number">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="landmark" class="w-4 h-4 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">Bank Details</h2>
                    <p class="text-xs text-[#565959]">For international settlements and payouts.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Bank Name <span class="text-[#c45500]">*</span></label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="e.g. GTBank">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Account Number <span class="text-[#c45500]">*</span></label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="10-digit account number">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#565959] mb-1.5">Account Name <span class="text-[#c45500]">*</span></label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Account holder name">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">Export Capacity</h2>
                    <p class="text-xs text-[#565959]">Tell us about your export readiness.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Monthly Capacity</label>
                        <input type="text" name="trade_capacity" value="{{ old('trade_capacity') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="e.g. 500kg monthly">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Export Markets</label>
                        <input type="text" name="export_markets" value="{{ old('export_markets') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="e.g. UK, US, EU">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">Upload Documents</h2>
                    <p class="text-xs text-[#565959]">Upload your business documents for export verification.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-xs text-[#565959]">Accepted formats: PDF, JPG, PNG (max 5MB each)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">CAC Certificate</label>
                        <input type="file" name="document_cac" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-[#565959] file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-gray-300 file:text-sm file:font-medium file:bg-[#f7f8f8] file:text-[#0f1111] hover:file:bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Logo</label>
                        <input type="file" name="document_logo" accept=".jpg,.jpeg,.png" class="w-full text-sm text-[#565959] file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-gray-300 file:text-sm file:font-medium file:bg-[#f7f8f8] file:text-[#0f1111] hover:file:bg-gray-100">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Valid ID (National ID/Passport) <span class="text-[#c45500]">*</span></label>
                        <input type="file" name="document_id" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm text-[#565959] file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-gray-300 file:text-sm file:font-medium file:bg-[#f7f8f8] file:text-[#0f1111] hover:file:bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Proof of Business Address <span class="text-[#c45500]">*</span></label>
                        <input type="file" name="document_address" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-sm text-[#565959] file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border file:border-gray-300 file:text-sm file:font-medium file:bg-[#f7f8f8] file:text-[#0f1111] hover:file:bg-gray-100">
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full amazon-btn text-base font-semibold py-3 rounded-lg border mb-8">
            Submit Export Upgrade
        </button>
    </form>
</div>
@endSection
