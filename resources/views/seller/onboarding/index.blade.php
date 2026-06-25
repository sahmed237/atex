@extends('layouts.buyer')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
<style>.iti { width: 100%; }</style>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('onboardingForm', () => ({
        step: 1,
        country: '{{ old('country', 'Nigeria') }}',
        codes: @php
            $codes = \Imujas9\World\Facades\Country::all()->mapWithKeys(fn($c) => [$c->name => $c->code]);
        @endphp {!! $codes->toJson() !!},
        states: [],
        iti: null,
        rejectedFields: {!! isset($rejectedFields) ? json_encode($rejectedFields->keys()->toArray()) : '[]' !!},
        hasChanges: false,
        
        async loadStates() {
            const code = this.codes[this.country];
            if (!code) { this.states = []; return; }
            const r = await fetch('{{ url("api/world/states") }}/' + code);
            this.states = await r.json();
        },
        
        init() {
            this.loadStates();
            const input = document.querySelector('#phone_input');
            if (input) {
                this.iti = window.intlTelInput(input, {
                    initialCountry: 'ng',
                    utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/utils.js',
                    separateDialCode: true,
                });
                this.syncPhoneCode();
                input.addEventListener('countrychange', () => this.checkChanges());
            }
            
            // Store initial values for rejected fields
            setTimeout(() => {
                const form = document.getElementById('onboardingForm');
                if (form) {
                    this.rejectedFields.forEach(field => {
                        let inputName = field === 'address' ? 'business_address' : field;
                        const el = form.querySelector('[name=\'' + inputName + '\']');
                        if (el && el.type !== 'file') {
                            el.dataset.initial = el.value;
                        }
                    });
                    this.checkChanges();
                }
            }, 100);
        },
        
        syncPhoneCode() {
            if (this.iti) {
                const code = this.codes[this.country];
                if (code) {
                    this.iti.setCountry(code.toLowerCase());
                }
            }
        },
        
        checkChanges() {
            if (this.rejectedFields.length === 0) {
                this.hasChanges = true;
                return;
            }
            
            let allChanged = true;
            const form = document.getElementById('onboardingForm');
            if (!form) return;
            
            this.rejectedFields.forEach(field => {
                let inputName = field === 'address' ? 'business_address' : field;
                const el = form.querySelector('[name=\'' + inputName + '\']');
                if (el) {
                    if (el.type === 'file') {
                        if (el.files.length === 0) allChanged = false;
                    } else {
                        if (el.value === el.dataset.initial) allChanged = false;
                    }
                }
            });
            
            this.hasChanges = allChanged;
        }
    }));
});
</script>

<div class="max-w-3xl mx-auto" x-data="onboardingForm()">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-[#0f1111]">Become a Local Seller</h1>
        <p class="text-sm text-[#565959]">Register your business to sell on Adamawa Ecommerce platform. Start locally, upgrade to export anytime.</p>
    </div>

    <div class="bg-[#f0f8f0] border border-[#007600] rounded-lg px-4 py-3 mb-4 flex items-start gap-3">
        <i data-lucide="info" class="w-5 h-5 text-[#007600] shrink-0 mt-0.5"></i>
        <div>
            <p class="text-sm font-medium text-[#0f1111]">Stage 1 of 2 — Local Seller</p>
            <p class="text-xs text-[#565959]">After this, you can upgrade to an export seller with additional verification to reach international buyers.</p>
        </div>
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

        @if(isset($profile) && $profile->verification_status === 'rejected')
        <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-4 flex items-start gap-3">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
            <div>
                <p class="text-sm font-bold text-red-800">Your KYC submission was rejected.</p>
                <p class="text-xs text-red-600 mt-1">Please review the fields below and correct the highlighted issues. You only need to fix the items with red warning messages.</p>
            </div>
        </div>
    @endif

    <form id="onboardingForm" action="{{ route('seller.onboarding.store') }}" method="POST" enctype="multipart/form-data" @input="checkChanges" @change="checkChanges">
        @csrf

        <!-- Wizard Progress -->
        <div class="mb-8 mt-2 relative">
            <div class="flex items-center justify-between relative z-10">
                <div class="absolute left-0 top-1/2 w-full h-1 bg-gray-200 -z-10 -translate-y-1/2 rounded"></div>
                <div class="absolute left-0 top-1/2 h-1 bg-[#007185] -z-10 -translate-y-1/2 rounded transition-all duration-300" :style="'width: ' + ((step - 1) / 3 * 100) + '%'"></div>
                
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors border-2 bg-white" :class="step >= 1 ? 'text-[#007185] border-[#007185]' : 'text-gray-400 border-gray-300'">1</div>
                    <span class="text-xs font-bold" :class="step >= 1 ? 'text-[#0f1111]' : 'text-gray-400'">Business</span>
                </div>
                
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors border-2 bg-white" :class="step >= 2 ? 'text-[#007185] border-[#007185]' : 'text-gray-400 border-gray-300'">2</div>
                    <span class="text-xs font-bold" :class="step >= 2 ? 'text-[#0f1111]' : 'text-gray-400'">Location</span>
                </div>
                
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors border-2 bg-white" :class="step >= 3 ? 'text-[#007185] border-[#007185]' : 'text-gray-400 border-gray-300'">3</div>
                    <span class="text-xs font-bold" :class="step >= 3 ? 'text-[#0f1111]' : 'text-gray-400'">Personal</span>
                </div>

                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-colors border-2 bg-white" :class="step >= 4 ? 'text-[#007185] border-[#007185]' : 'text-gray-400 border-gray-300'">4</div>
                    <span class="text-xs font-bold" :class="step >= 4 ? 'text-[#0f1111]' : 'text-gray-400'">Documents</span>
                </div>
            </div>
        </div>

        <!-- Step 1: Business Information -->
        <div x-show="step === 1" x-transition.opacity.duration.300ms>
            <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4 shadow-sm">
                <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3 bg-[#f7f8f8]">
                    <div class="w-9 h-9 rounded-lg bg-white shadow-sm border border-[#e7e7e7] flex items-center justify-center">
                        <i data-lucide="store" class="w-4 h-4 text-[#007185]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#0f1111]">Business Information</h2>
                        <p class="text-xs text-[#565959]">Basic details about your business.</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Name <span class="text-[#c45500]">*</span></label>
                        
<input type="text" name="business_name" value="{{ old('business_name', isset($profile) ? $profile->business_name ?? '' : '') }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Your business name">
                        @if(isset($rejectedFields) && $rejectedFields->has('business_name'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['business_name']->comment }}</span></p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Brand Name</label>
                        
<input type="text" name="seller_brand_name" value="{{ old('seller_brand_name', isset($profile) ? $profile->seller_brand_name ?? '' : '') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Your brand name (if different from business name)">
                        @if(isset($rejectedFields) && $rejectedFields->has('seller_brand_name'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['seller_brand_name']->comment }}</span></p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Description</label>
                        
<textarea name="business_description" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Tell us about your business...">{{ old('business_description', isset($profile) ? $profile->business_description ?? '' : '') }}</textarea>
                        @if(isset($rejectedFields) && $rejectedFields->has('business_description'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['business_description']->comment }}</span></p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Category <span class="text-[#c45500]">*</span></label>
                        
<select name="business_category" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" {{ old('business_category', isset($profile) ? $profile->business_category ?? '' : '') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @if(isset($rejectedFields) && $rejectedFields->has('business_category'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['business_category']->comment }}</span></p>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button type="button" @click="step = 2" class="amazon-btn text-base font-semibold py-2.5 px-8 rounded-lg shadow-sm">
                    Next Step
                </button>
            </div>
        </div>

        <!-- Step 2: Location Information -->
        <div x-show="step === 2" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4 shadow-sm">
                <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3 bg-[#f7f8f8]">
                    <div class="w-9 h-9 rounded-lg bg-white shadow-sm border border-[#e7e7e7] flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4 text-[#007185]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#0f1111]">Business Location</h2>
                        <p class="text-xs text-[#565959]">Where is your business located?</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Address <span class="text-[#c45500]">*</span></label>
                        
<textarea name="business_address" rows="2" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Street address">{{ old('business_address', isset($profile) ? $profile->address ?? '' : '') }}</textarea>
                        @if(isset($rejectedFields) && $rejectedFields->has('business_address'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['business_address']->comment }}</span></p>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">Country <span class="text-[#c45500]">*</span></label>
                            
<select name="country" x-model="country" @change="loadStates(); syncPhoneCode();" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @foreach(\Imujas9\World\Facades\Country::all() as $c)
                                    <option value="{{ $c->name }}" {{ old('country', 'Nigeria') == $c->name ? 'selected' : '' }}>{{ $c->flag }} {{ $c->name }}</option>
                                @endforeach
                            </select>
                        @if(isset($rejectedFields) && $rejectedFields->has('country'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['country']->comment }}</span></p>
                        @endif
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">State <span class="text-[#c45500]">*</span></label>
                            
<select name="state" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                <option value="">Select state</option>
                                <template x-for="s in states" :key="s.code">
                                    <option :value="s.name" x-text="s.name" :selected="s.name === '{{ old('state', isset($profile) ? $profile->state ?? '' : '') }}'"></option>
                                </template>
                            </select>
                        @if(isset($rejectedFields) && $rejectedFields->has('state'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['state']->comment }}</span></p>
                        @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">LGA</label>
                            <input type="text" name="lga" value="{{ old('lga', isset($profile) ? $profile->lga ?? '' : '') }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Local Government Area">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">City</label>
                            
<input type="text" name="city" value="{{ old('city', isset($profile) ? $profile->city ?? '' : '') }}"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="City">
                        @if(isset($rejectedFields) && $rejectedFields->has('city'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['city']->comment }}</span></p>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <button type="button" @click="step = 1" class="text-[#007185] font-semibold py-2 px-4 hover:underline">
                    &larr; Back
                </button>
                <button type="button" @click="step = 3" class="amazon-btn text-base font-semibold py-2.5 px-8 rounded-lg shadow-sm">
                    Next Step
                </button>
            </div>
        </div>

        <!-- Step 3: Personal Details -->
        <div x-show="step === 3" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4 shadow-sm">
                <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3 bg-[#f7f8f8]">
                    <div class="w-9 h-9 rounded-lg bg-white shadow-sm border border-[#e7e7e7] flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-[#007185]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#0f1111]">Personal Details</h2>
                        <p class="text-xs text-[#565959]">Who is managing this business?</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">Full Name (as on ID) <span class="text-[#c45500]">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name', isset($profile) ? $profile->kyc->full_name ?? '' : '') }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="John Michael Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">Date of Birth <span class="text-[#c45500]">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', isset($profile) ? $profile->kyc->date_of_birth ?? '' : '') }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">Nationality <span class="text-[#c45500]">*</span></label>
                            <select name="nationality" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                <option value="">Select nationality</option>
                                @foreach(\Imujas9\World\Facades\Country::all() as $c)
                                    <option value="{{ $c->name }}" {{ old('nationality', 'Nigeria') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">Phone Number <span class="text-[#c45500]">*</span></label>
                            
<div wire:ignore>
                                <input type="text" name="phone" id="phone_input" value="{{ old('phone', isset($profile) ? $profile->phone ?? '' : '') }}" required
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="+234 XXX XXX XXXX">
                        @if(isset($rejectedFields) && $rejectedFields->has('phone'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['phone']->comment }}</span></p>
                        @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Residential Address <span class="text-[#c45500]">*</span></label>
                        <input type="text" name="residential_address" value="{{ old('residential_address', isset($profile) ? $profile->kyc->residential_address ?? '' : '') }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="123 Main St, City, State">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="button" @click="step = 2" class="text-[#007185] font-semibold py-2 px-4 hover:underline">
                    &larr; Back
                </button>
                <button type="button" @click="step = 4" class="amazon-btn text-base font-semibold py-2.5 px-8 rounded-lg shadow-sm">
                    Next Step
                </button>
            </div>
        </div>

        <!-- Step 4: Verification Documents -->
        <div x-show="step === 4" style="display: none;" x-transition.opacity.duration.300ms>
            <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4 shadow-sm">
                <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3 bg-[#f7f8f8]">
                    <div class="w-9 h-9 rounded-lg bg-white shadow-sm border border-[#e7e7e7] flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#007185]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#0f1111]">Verification Documents</h2>
                        <p class="text-xs text-[#565959]">Please provide documents to verify your identity.</p>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">Document Type <span class="text-[#c45500]">*</span></label>
                            
<select name="id_type" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @php $selectedIdType = old('id_type', isset($profile) && $profile->kyc ? $profile->kyc->id_type : ''); @endphp
                                <option value="">Select ID type</option>
                                <option value="nin" {{ $selectedIdType === 'nin' ? 'selected' : '' }}>National ID (NIN)</option>
                                <option value="passport" {{ $selectedIdType === 'passport' ? 'selected' : '' }}>International Passport</option>
                                <option value="drivers" {{ $selectedIdType === 'drivers' ? 'selected' : '' }}>Driver's License</option>
                                <option value="voter" {{ $selectedIdType === 'voter' ? 'selected' : '' }}>Voter's Card</option>
                            </select>
                        @if(isset($rejectedFields) && $rejectedFields->has('id_type'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['id_type']->comment }}</span></p>
                        @endif
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#565959] mb-1.5">ID Number <span class="text-[#c45500]">*</span></label>
                            
<input type="text" name="id_number" value="{{ old('id_number', isset($profile) ? $profile->kyc->id_number ?? '' : '') }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Enter ID number">
                        @if(isset($rejectedFields) && $rejectedFields->has('id_number'))
                            <p class="text-xs text-red-600 mt-2 font-semibold flex items-start"><i data-lucide="alert-circle" class="w-3.5 h-3.5 mr-1 shrink-0 mt-0.5"></i> <span>{{ $rejectedFields['id_number']->comment }}</span></p>
                        @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="button" @click="step = 3" class="text-[#007185] font-semibold py-2 px-4 hover:underline">
                    &larr; Back
                </button>
                <button type="submit" class="amazon-btn text-base font-semibold py-2.5 px-8 rounded-lg shadow-sm transition-all"
                        :disabled="!hasChanges" :class="!hasChanges ? 'opacity-50 cursor-not-allowed grayscale' : ''">
                    Submit Registration
                </button>
            </div>
        </div>
    </form>
</div>
@endSection
