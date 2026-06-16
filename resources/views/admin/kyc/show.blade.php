@extends('layouts.admin')

@section('title', 'KYC Details | Adamawa Export Market')
@section('header_title', 'KYC Details')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">KYC Verification</h1>
        <p class="text-slate-500 text-sm">Detailed KYC profile for {{ $profile->business_name ?? $profile->company_name ?? ($profile->user->name ?? 'User') }}</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.kyc.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-medium flex items-center hover:bg-slate-50 transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
            Back to KYC List
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Basic Info & Actions -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Profile Summary -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center relative overflow-hidden">
            @if($profile->verification_status === 'approved')
                <div class="absolute top-0 right-0 left-0 h-2 bg-emerald-500"></div>
            @elseif($profile->verification_status === 'rejected')
                <div class="absolute top-0 right-0 left-0 h-2 bg-red-500"></div>
            @else
                <div class="absolute top-0 right-0 left-0 h-2 bg-amber-500"></div>
            @endif

            <div class="w-24 h-24 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100 mx-auto mb-6 flex items-center justify-center">
                <i data-lucide="building-2" class="w-10 h-10"></i>
            </div>
            
            <h2 class="text-xl font-bold text-slate-800 mb-1">{{ $profile->business_name ?? $profile->company_name ?? $profile->full_name ?? 'N/A' }}</h2>
            <p class="text-slate-500 text-sm mb-4">{{ $profile->user->email ?? 'N/A' }}</p>
            
            <div class="inline-flex px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-6
                {{ $profile->verification_status === 'approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                {{ $profile->verification_status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                {{ $profile->verification_status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
            ">
                @if($profile->verification_status === 'approved')
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>
                @elseif($profile->verification_status === 'rejected')
                    <i data-lucide="x-circle" class="w-4 h-4 mr-1.5"></i>
                @else
                    <i data-lucide="clock" class="w-4 h-4 mr-1.5"></i>
                @endif
                {{ ucfirst($profile->verification_status) }}
            </div>

            <div class="pt-6 border-t border-slate-50 text-left space-y-4">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Account Owner</p>
                    <p class="text-sm text-slate-700 font-medium flex items-center">
                        <i data-lucide="user" class="w-4 h-4 mr-2 text-slate-400"></i>
                        {{ $profile->user->name ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Profile Type</p>
                    <p class="text-sm text-slate-700 font-medium flex items-center">
                        <i data-lucide="tag" class="w-4 h-4 mr-2 text-slate-400"></i>
                        {{ ucfirst($type) }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Address</p>
                    <p class="text-sm text-slate-700 font-medium flex items-start">
                        <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-slate-400 mt-0.5 shrink-0"></i>
                        {{ $profile->address ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Verification Actions -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider border-b border-slate-100 pb-3">Verification Actions</h3>
            
            <form action="{{ route('admin.kyc.review') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="profile_type" value="{{ $type }}">
                <input type="hidden" name="profile_id" value="{{ $profile->id }}">
                <input type="hidden" name="return_to" value="kyc">
                
                @if($profile->verification_status !== 'approved')
                    <button type="submit" name="status" value="approved" class="w-full px-4 py-3 bg-emerald-600 text-white rounded-xl font-bold flex items-center justify-center hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-200">
                        <i data-lucide="shield-check" class="w-5 h-5 mr-2"></i>
                        Approve KYC
                    </button>
                @endif
                
                @if($profile->verification_status !== 'pending')
                    <button type="submit" name="status" value="pending" class="w-full px-4 py-3 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-bold flex items-center justify-center hover:bg-amber-100 transition-colors">
                        <i data-lucide="clock" class="w-5 h-5 mr-2"></i>
                        Put on Hold
                    </button>
                @endif
                
                @if($profile->verification_status !== 'rejected')
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <label for="rejection_reason" class="block text-sm font-bold text-slate-700 mb-2">Rejection Reason</label>
                        <textarea id="rejection_reason" name="reason" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm resize-none focus:border-red-300 focus:ring-1 focus:ring-red-200" placeholder="Specify which documents/issues need attention..."></textarea>
                        <button type="submit" name="status" value="rejected" class="w-full mt-2 px-4 py-3 bg-red-50 text-red-700 border border-red-200 rounded-xl font-bold flex items-center justify-center hover:bg-red-100 transition-colors">
                            <i data-lucide="x-circle" class="w-5 h-5 mr-2"></i>
                            Reject KYC
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Right Column: Verification Data & Docs -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Nigerian KYC Info -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mr-4">
                        <i data-lucide="fingerprint" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Regulatory Information</h3>
                </div>
                @if($profile->verification_status !== 'approved')
                    <div class="flex items-center gap-2">
                        <button type="button" id="approve-all-regulatory" class="px-4 py-2 border border-emerald-300 text-emerald-700 rounded-xl text-xs font-bold hover:bg-emerald-50 transition-colors">Approve All</button>
                        <button type="submit" form="regulatory-form" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-colors">Save Reviews</button>
                    </div>
                @endif
            </div>

            <form id="regulatory-form" action="{{ route('admin.kyc.review-regulatory') }}" method="POST">
                @csrf
                <input type="hidden" name="profile_type" value="{{ $type }}">
                <input type="hidden" name="profile_id" value="{{ $profile->id }}">

                @php
                    $reviews = $profile->regulatory_reviews ?? [];
                    $regulatoryFields = [
                        'registration_number' => ['label' => 'CAC Registration', 'icon' => 'file-text', 'value' => $profile->registration_number ?? 'Not provided'],
                        'tax_number' => ['label' => 'Tax Identification (TIN)', 'icon' => 'landmark', 'value' => $profile->tax_number ?? 'Not provided'],
                        'bvn' => ['label' => 'Bank Verification (BVN)', 'icon' => 'credit-card', 'value' => $profile->bvn ?? 'Not provided'],
                        'nin' => ['label' => 'National Identity (NIN)', 'icon' => 'user-check', 'value' => $profile->nin ?? 'Not provided'],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($regulatoryFields as $field => $info)
                        @php
                            $fieldReview = $reviews[$field] ?? null;
                            $status = $fieldReview['status'] ?? null;
                            $isApproved = $status === 'approved';
                            $isRejected = $status === 'rejected';
                        @endphp
                        <div class="p-4 rounded-2xl {{ $isApproved ? 'bg-emerald-50/50 border border-emerald-200' : ($isRejected ? 'bg-red-50/50 border border-red-200' : 'bg-slate-50 border border-slate-100') }}">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider flex items-center">
                                    <i data-lucide="{{ $info['icon'] }}" class="w-3.5 h-3.5 mr-1.5 text-slate-400"></i>
                                    {{ $info['label'] }}
                                </p>
                                @if($profile->verification_status !== 'approved')
                                    <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white divide-x divide-slate-200">
                                        <input type="radio" name="fields[{{ $field }}][status]" value="approved" class="hidden peer/ok" id="reg_{{ $field }}_ok" {{ $isApproved ? 'checked' : '' }}>
                                        <label for="reg_{{ $field }}_ok" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors peer-checked/ok:bg-emerald-600 peer-checked/ok:text-white text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 select-none">Approve</label>
                                        <input type="radio" name="fields[{{ $field }}][status]" value="rejected" class="hidden peer/no" id="reg_{{ $field }}_no" {{ $isRejected ? 'checked' : '' }}>
                                        <label for="reg_{{ $field }}_no" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors peer-checked/no:bg-red-600 peer-checked/no:text-white text-slate-500 hover:text-red-700 hover:bg-red-50 select-none">Reject</label>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-emerald-600">Approved</span>
                                @endif
                            </div>
                            <p class="text-lg font-bold text-slate-800 {{ in_array($field, ['bvn','nin']) ? 'font-mono tracking-widest' : '' }}">{{ $info['value'] }}</p>
                            @if($profile->verification_status !== 'approved')
                                <textarea name="fields[{{ $field }}][comment]" rows="1" class="w-full mt-2 px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs resize-none {{ $isRejected ? 'block' : ($fieldReview ? 'block' : 'hidden') }} comment-input" placeholder="Reason if rejected...">{{ $fieldReview['comment'] ?? '' }}</textarea>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Bank Details -->
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-3 uppercase tracking-wider flex items-center">
                        <i data-lucide="wallet" class="w-4 h-4 mr-2 text-emerald-500"></i> Bank Details
                    </h4>
                    @php
                        $bankFields = [
                            'bank_name' => ['label' => 'Bank Name', 'value' => $profile->bank_name ?? 'Not provided'],
                            'account_number' => ['label' => 'Account Number', 'value' => $profile->account_number ?? 'Not provided', 'mono' => true],
                            'account_name' => ['label' => 'Account Name', 'value' => $profile->account_name ?? 'Not provided'],
                        ];
                    @endphp
                    <div class="space-y-3">
                        @foreach($bankFields as $field => $info)
                            @php
                                $fieldReview = $reviews[$field] ?? null;
                                $status = $fieldReview['status'] ?? null;
                                $isApproved = $status === 'approved';
                                $isRejected = $status === 'rejected';
                            @endphp
                            <div class="p-3 rounded-xl {{ $isApproved ? 'bg-emerald-50/50 border border-emerald-200' : ($isRejected ? 'bg-red-50/50 border border-red-200' : 'bg-slate-50 border border-slate-100') }}">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-slate-500 font-medium mb-0.5">{{ $info['label'] }}</p>
                                        <p class="text-sm font-bold text-slate-800 {{ !empty($info['mono']) ? 'font-mono' : '' }}">{{ $info['value'] }}</p>
                                    </div>
                                    @if($profile->verification_status !== 'approved')
                                        <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white divide-x divide-slate-200 shrink-0">
                                            <input type="radio" name="fields[{{ $field }}][status]" value="approved" class="hidden peer/ok" id="reg_{{ $field }}_ok" {{ $isApproved ? 'checked' : '' }}>
                                            <label for="reg_{{ $field }}_ok" class="px-2 py-1 text-xs font-semibold cursor-pointer transition-colors peer-checked/ok:bg-emerald-600 peer-checked/ok:text-white text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 select-none">Approve</label>
                                            <input type="radio" name="fields[{{ $field }}][status]" value="rejected" class="hidden peer/no" id="reg_{{ $field }}_no" {{ $isRejected ? 'checked' : '' }}>
                                            <label for="reg_{{ $field }}_no" class="px-2 py-1 text-xs font-semibold cursor-pointer transition-colors peer-checked/no:bg-red-600 peer-checked/no:text-white text-slate-500 hover:text-red-700 hover:bg-red-50 select-none">Reject</label>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-emerald-600 shrink-0">Approved</span>
                                    @endif
                                </div>
                                @if($profile->verification_status !== 'approved')
<textarea name="fields[{{ $field }}][comment]" rows="1" class="w-full mt-2 px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs resize-none {{ $isRejected ? 'block' : 'hidden' }} comment-input" placeholder="Reason if rejected...">{{ $fieldReview['comment'] ?? '' }}</textarea>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </form>
        </div>

        <!-- Uploaded Documents -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center mr-4">
                        <i data-lucide="folder-open" class="w-5 h-5 text-sky-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Uploaded Documents</h3>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-lg">{{ $documents->count() }} Files</span>
            </div>

            @if($documents->count() > 0)
                <form action="{{ route('admin.kyc.document.review-all') }}" method="POST">
                    @csrf
                    <input type="hidden" name="profile_type" value="{{ $type }}">
                    <input type="hidden" name="profile_id" value="{{ $profile->id }}">

                    @if($profile->verification_status !== 'approved')
                        <div class="flex gap-2 mb-5">
                            <button type="submit" name="status" value="approved" class="flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-colors shadow-sm"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Approve All</button>
                            <button type="submit" name="status" value="rejected" class="flex items-center gap-1.5 px-4 py-2 bg-white text-red-600 border border-red-200 rounded-xl text-xs font-bold hover:bg-red-50 transition-colors"><i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Reject All</button>
                        </div>
                    @endif

                    <div class="space-y-2">
                        @foreach($documents as $doc)
                            @php
                                $isApproved = $doc->status === 'approved';
                                $isRejected = $doc->status === 'rejected';
                                $borderClass = $isApproved ? 'border-emerald-200 bg-emerald-50/30' : ($isRejected ? 'border-red-200 bg-red-50/30' : 'border-slate-200 bg-white');
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl border {{ $borderClass }} transition-colors">
                                <div class="flex items-center min-w-0 gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center shrink-0 shadow-sm">
                                        @php
                                            $ext = pathinfo($doc->path, PATHINFO_EXTENSION);
                                            $icon = 'file-text';
                                            $color = 'text-slate-400';
                                            if($isApproved) $color = 'text-emerald-500';
                                            if($isRejected) $color = 'text-red-400';
                                            if(in_array($ext, ['jpg','jpeg','png'])) $icon = 'image';
                                            if(in_array($ext, ['pdf'])) $icon = 'file-check-2';
                                        @endphp
                                        <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $color }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $doc->title }}</p>
                                        <div class="flex items-center gap-2 text-xs text-slate-500">
                                            <span class="uppercase tracking-wider">{{ $ext }}</span>
                                            @if($profile->verification_status !== 'approved')
                                                <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white divide-x divide-slate-200">
                                                    <input type="radio" name="documents[{{ $doc->id }}]" value="approved" class="hidden peer/ok" id="doc_ok_{{ $doc->id }}" {{ $isApproved ? 'checked' : '' }}>
                                                    <label for="doc_ok_{{ $doc->id }}" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors peer-checked/ok:bg-emerald-600 peer-checked/ok:text-white text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 select-none">Approve</label>
                                                    <input type="radio" name="documents[{{ $doc->id }}]" value="rejected" class="hidden peer/no" id="doc_no_{{ $doc->id }}" {{ $isRejected ? 'checked' : '' }}>
                                                    <label for="doc_no_{{ $doc->id }}" class="px-2.5 py-1 text-xs font-semibold cursor-pointer transition-colors peer-checked/no:bg-red-600 peer-checked/no:text-white text-slate-500 hover:text-red-700 hover:bg-red-50 select-none">Reject</label>
                                                </div>
                                            @endif
                                        </div>
                                        @if($doc->review_comment)
                                            <p class="text-xs text-red-600 mt-0.5">{{ $doc->review_comment }}</p>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-primary-600 hover:text-white hover:border-primary-600 transition-all shrink-0">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if($profile->verification_status !== 'approved')
                        <div class="mt-5 pt-5 border-t border-slate-100">
                            <label for="bulk_note" class="block text-sm font-semibold text-slate-700 mb-1.5">Notes for applicant</label>
                            <div class="flex gap-2">
                                <textarea id="bulk_note" name="comment" rows="1" class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-sm resize-none" placeholder="Tell them what needs to be fixed..."></textarea>
                                <button type="submit" name="status" value="rejected" class="px-5 py-2 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-colors shadow-sm whitespace-nowrap">Reject & Send Notes</button>
                            </div>
                        </div>
                    @endif
                </form>
            @else
                <div class="text-center py-12 px-6 border-2 border-dashed border-slate-200 rounded-3xl bg-slate-50/50">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i data-lucide="file-x" class="w-8 h-8 text-slate-300"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600 mb-1">No Documents Uploaded</p>
                    <p class="text-xs text-slate-500">This user has not provided any verification documents.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
document.querySelectorAll('input[type="radio"][name^="fields["]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var container = this.closest('.p-4, .p-3');
        var textarea = container ? container.querySelector('textarea.comment-input') : null;
        if (textarea) {
            textarea.classList.toggle('hidden', this.value !== 'rejected');
            if (this.value === 'rejected') textarea.focus();
        }
    });
});

document.getElementById('approve-all-regulatory')?.addEventListener('click', function() {
    document.querySelectorAll('#regulatory-form input[type="radio"][value="approved"]').forEach(function(radio) {
        radio.checked = true;
        var container = radio.closest('.p-4, .p-3');
        var textarea = container ? container.querySelector('textarea.comment-input') : null;
        if (textarea) textarea.classList.add('hidden');
    });
});
</script>
@endpush
@endsection
