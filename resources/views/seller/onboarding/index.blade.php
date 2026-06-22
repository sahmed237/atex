@extends('layouts.buyer')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/css/intlTelInput.css">
<style>.iti { width: 100%; }</style>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.4/build/js/intlTelInput.min.js"></script>

<div class="max-w-3xl mx-auto" x-data="{
    country: '{{ old('country', 'Nigeria') }}',
    codes: @php
        $codes = \Imujas9\World\Facades\Country::all()->mapWithKeys(fn($c) => [$c->name => $c->code]);
    @endphp {{ $codes->toJson() }},
    states: [],
    iti: null,
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
        }
    },
    syncPhoneCode() {
        if (this.iti) {
            const code = this.codes[this.country];
            if (code) {
                this.iti.setCountry(code.toLowerCase());
            }
        }
    }
}">
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

    <form action="{{ route('seller.onboarding.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
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
                    <input type="text" name="business_name" value="{{ old('business_name') }}" required
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Your business name">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#565959] mb-1.5">Brand Name</label>
                    <input type="text" name="seller_brand_name" value="{{ old('seller_brand_name') }}"
                           class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Your brand name (if different from business name)">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Description</label>
                    <textarea name="business_description" rows="3" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Tell us about your business...">{{ old('business_description') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[#565959] mb-1.5">Business Category <span class="text-[#c45500]">*</span></label>
                    <select name="business_category" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                        <option value="">Select category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}" {{ old('business_category') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
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
                    <textarea name="business_address" rows="2" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Street address">{{ old('business_address') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Country <span class="text-[#c45500]">*</span></label>
                        <select name="country" x-model="country" @change="loadStates(); syncPhoneCode();" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                            @foreach(\Imujas9\World\Facades\Country::all() as $c)
                                <option value="{{ $c->name }}" {{ old('country', 'Nigeria') == $c->name ? 'selected' : '' }}>{{ $c->flag }} {{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">State <span class="text-[#c45500]">*</span></label>
                        <select name="state" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                            <option value="">Select state</option>
                            <template x-for="s in states" :key="s.code">
                                <option :value="s.name" x-text="s.name" :selected="s.name === '{{ old('state') }}'"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">LGA</label>
                        <input type="text" name="lga" value="{{ old('lga') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="Local Government Area">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">City</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="City">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
            <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="phone" class="w-4 h-4 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">Contact Information</h2>
                    <p class="text-xs text-[#565959]">How buyers can reach you.</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">Phone Number <span class="text-[#c45500]">*</span></label>
                        <div wire:ignore>
                            <input type="text" name="phone" id="phone_input" value="{{ old('phone') }}" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="+234 XXX XXX XXXX">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#565959] mb-1.5">NIN <span class="text-[#c45500]">*</span></label>
                        <input type="text" name="nin" value="{{ old('nin') }}" required maxlength="11"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors" placeholder="11-digit National ID Number">
                    </div>
                </div>
                <p class="text-xs text-[#565959] flex items-start gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-[#007185] shrink-0 mt-0.5"></i>
                    Full export compliance (BVN, bank &amp; documents) is only required later if you upgrade to an exporter.
                </p>
            </div>
        </div>

        <button type="submit" class="w-full amazon-btn text-base font-semibold py-3 rounded-lg border mb-8">
            Register as Local Seller
        </button>
    </form>
</div>
@endSection
