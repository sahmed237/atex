@extends('layouts.buyer')

@section('content')
<div x-data="{ tab: '{{ request()->query('tab', 'profile') }}' }">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-[#0f1111]">Account Settings</h1>
        <p class="text-sm text-[#565959]">Manage your profile and security preferences</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Profile Card + Tab Nav -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4">
                <div class="h-20 bg-gradient-to-br from-[#1a2a3a] to-[#0f1a26]"></div>
                <div class="px-5 pb-5 -mt-10 text-center">
                    <div class="inline-block p-1 bg-white rounded-xl mb-3 shadow-sm border border-[#e7e7e7]">
                        <div class="w-20 h-20 rounded-lg bg-[#f0f2f2] flex items-center justify-center font-bold text-2xl text-[#0f1111]">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    </div>
                    <h3 class="text-base font-bold text-[#0f1111]">{{ $user->name }}</h3>
                    <p class="text-xs text-[#565959] mb-4">{{ $user->email }}</p>
                    <div class="bg-[#f7f8f8] rounded-lg p-3 flex items-center gap-2 text-left">
                        <i data-lucide="clock" class="w-4 h-4 text-[#565959] shrink-0"></i>
                        <div>
                            <p class="text-[10px] font-bold text-[#565959] uppercase tracking-widest">Account Created</p>
                            <p class="text-xs font-bold text-[#0f1111]">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden">
                <button @click="tab = 'profile'" class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors" :class="tab === 'profile' ? 'bg-[#f0f2f2] text-[#0f1111]' : 'text-[#565959] hover:bg-[#f7f8f8]'">
                    <i data-lucide="user" class="w-4 h-4" :class="tab === 'profile' ? 'text-[#007185]' : ''"></i>
                    <span class="text-sm font-medium">Profile Details</span>
                </button>
                <button @click="tab = '2fa'" class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors border-t border-[#e7e7e7]" :class="tab === '2fa' ? 'bg-[#f0f2f2] text-[#0f1111]' : 'text-[#565959] hover:bg-[#f7f8f8]'">
                    <i data-lucide="shield-check" class="w-4 h-4" :class="tab === '2fa' ? 'text-[#007185]' : ''"></i>
                    <span class="text-sm font-medium">Two-Factor Authentication</span>
                    @if($user->hasTwoFactorEnabled())
                        <span class="ml-auto text-[10px] font-bold text-[#007600] bg-[#f0f8f0] px-2 py-0.5 rounded">Active</span>
                    @else
                        <span class="ml-auto text-[10px] font-bold text-[#c45500] bg-[#fff5f0] px-2 py-0.5 rounded">Off</span>
                    @endif
                </button>
                <button @click="tab = 'password'" class="w-full flex items-center gap-3 px-4 py-3 text-left transition-colors border-t border-[#e7e7e7]" :class="tab === 'password' ? 'bg-[#f0f2f2] text-[#0f1111]' : 'text-[#565959] hover:bg-[#f7f8f8]'">
                    <i data-lucide="key-round" class="w-4 h-4" :class="tab === 'password' ? 'text-[#007185]' : ''"></i>
                    <span class="text-sm font-medium">Change Password</span>
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="lg:col-span-3">
            <!-- Profile Details -->
            <div x-show="tab === 'profile'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-[#007185]"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-[#0f1111]">Profile Details</h2>
                            <p class="text-xs text-[#565959]">Manage your personal and buyer information.</p>
                        </div>
                    </div>

                    <form action="{{ route('buyer.profile.update-info') }}" method="POST" class="p-6">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Full Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('name') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Email Address</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('email') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Account Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('phone') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Business/Buyer Phone</label>
                                <input type="text" name="phone_number" value="{{ old('phone_number', $buyerProfile->phone_number) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('phone_number') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Gender</label>
                                <select name="gender" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors appearance-none">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', $buyerProfile->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $buyerProfile->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $buyerProfile->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Shipping Address</label>
                                <textarea name="shipping_address" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">{{ old('shipping_address', $buyerProfile->shipping_address) }}</textarea>
                                @error('shipping_address') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Billing Address</label>
                                <textarea name="billing_address" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">{{ old('billing_address', $buyerProfile->billing_address) }}</textarea>
                                @error('billing_address') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">City</label>
                                <input type="text" name="city" value="{{ old('city', $buyerProfile->city) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('city') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">State / Province</label>
                                <input type="text" name="state" value="{{ old('state', $buyerProfile->state) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('state') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Zip Code</label>
                                <input type="text" name="zip_code" value="{{ old('zip_code', $buyerProfile->zip_code) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('zip_code') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Country</label>
                                <input type="text" name="country" value="{{ old('country', $buyerProfile->country) }}"
                                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                                @error('country') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="amazon-btn text-sm font-semibold px-6 py-2.5 rounded-lg border">
                                Save Profile Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Two-Factor Authentication -->
            <div x-show="tab === '2fa'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-4 h-4 text-[#007185]"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm font-bold text-[#0f1111]">Two-Factor Authentication</h2>
                                    @if($user->hasTwoFactorEnabled())
                                        <span class="text-[10px] font-bold text-[#007600] bg-[#f0f8f0] px-2 py-0.5 rounded">Active</span>
                                    @endif
                                </div>
                                <p class="text-xs text-[#565959]">Add an additional layer of security using an authenticator app.</p>
                            </div>
                        </div>
                        <a href="{{ route('buyer.profile.2fa') }}" class="amazon-btn text-sm font-semibold px-5 py-2 rounded-lg border flex items-center gap-1.5">
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            Manage 2FA
                        </a>
                    </div>
                    <div class="p-6 bg-[#f7f8f8]">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <p class="text-sm text-[#0f1111] leading-relaxed">
                                    Two-factor authentication (2FA) is a core security requirement (ISO 27001). When enabled, you will be prompted for a secure, random token from your mobile device during login.
                                </p>
                            </div>
                            @if(!$user->hasTwoFactorEnabled())
                                <div class="flex items-center text-[#c45500] bg-[#fff5f0] px-4 py-2 rounded-lg text-xs font-bold shrink-0">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 mr-1.5"></i>
                                    Not yet enabled
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div x-show="tab === 'password'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden">
                    <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[#f0f2f2] flex items-center justify-center">
                            <i data-lucide="key-round" class="w-4 h-4 text-[#007185]"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-[#0f1111]">Change Password</h2>
                            <p class="text-xs text-[#565959]">Ensure your account is using a long, random password to stay secure.</p>
                        </div>
                    </div>

                    <form action="{{ route('buyer.profile.password') }}" method="POST" class="p-6"
                          x-data="{
                            password: '',
                            minLen: {{ \App\Models\Setting::get('password_min_length', 6) }},
                            get hasMinLen() { return this.password.length >= this.minLen },
                            get hasUpper() { return /[A-Z]/.test(this.password) },
                            get hasLower() { return /[a-z]/.test(this.password) },
                            get hasNumber() { return /[0-9]/.test(this.password) },
                            get hasSpecial() { return /[!@#$%^&*(),.?\':{}|<>]/.test(this.password) }
                          }">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-[#565959] mb-1.5">Current Password</label>
                                <x-password-input name="current_password" required placeholder="Current Password" />
                                @error('current_password') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-[#565959] mb-1.5">New Password</label>
                                    <x-password-input name="password" required x-model="password" placeholder="New Password" />
                                    @error('password') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-[#565959] mb-1.5">Confirm New Password</label>
                                    <x-password-input name="password_confirmation" required placeholder="Confirm New Password" />
                                </div>
                            </div>

                            <div class="bg-[#f7f8f8] rounded-lg p-5 border border-[#e7e7e7]">
                                <p class="text-[10px] font-bold text-[#565959] uppercase tracking-widest mb-3">Password Requirements</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-6">
                                    <div class="flex items-center gap-2 transition-all duration-300" :class="hasMinLen ? 'text-[#007600]' : 'text-[#565959]'">
                                        <div class="w-4 h-4 rounded-full flex items-center justify-center border-2 transition-colors shrink-0" :class="hasMinLen ? 'bg-[#007600] border-[#007600]' : 'border-gray-300'">
                                            <i data-lucide="check" class="w-2.5 h-2.5 text-white" x-show="hasMinLen"></i>
                                        </div>
                                        <span class="text-xs font-medium">At least <span x-text="minLen"></span> characters</span>
                                    </div>
                                    @if(\App\Models\Setting::get('password_require_uppercase') == '1')
                                        <div class="flex items-center gap-2 transition-all duration-300" :class="hasUpper ? 'text-[#007600]' : 'text-[#565959]'">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center border-2 transition-colors shrink-0" :class="hasUpper ? 'bg-[#007600] border-[#007600]' : 'border-gray-300'">
                                                <i data-lucide="check" class="w-2.5 h-2.5 text-white" x-show="hasUpper"></i>
                                            </div>
                                            <span class="text-xs font-medium">Contains uppercase letter</span>
                                        </div>
                                    @endif
                                    @if(\App\Models\Setting::get('password_require_lowercase') == '1')
                                        <div class="flex items-center gap-2 transition-all duration-300" :class="hasLower ? 'text-[#007600]' : 'text-[#565959]'">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center border-2 transition-colors shrink-0" :class="hasLower ? 'bg-[#007600] border-[#007600]' : 'border-gray-300'">
                                                <i data-lucide="check" class="w-2.5 h-2.5 text-white" x-show="hasLower"></i>
                                            </div>
                                            <span class="text-xs font-medium">Contains lowercase letter</span>
                                        </div>
                                    @endif
                                    @if(\App\Models\Setting::get('password_require_number') == '1')
                                        <div class="flex items-center gap-2 transition-all duration-300" :class="hasNumber ? 'text-[#007600]' : 'text-[#565959]'">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center border-2 transition-colors shrink-0" :class="hasNumber ? 'bg-[#007600] border-[#007600]' : 'border-gray-300'">
                                                <i data-lucide="check" class="w-2.5 h-2.5 text-white" x-show="hasNumber"></i>
                                            </div>
                                            <span class="text-xs font-medium">Contains numeric character</span>
                                        </div>
                                    @endif
                                    @if(\App\Models\Setting::get('password_require_special') == '1')
                                        <div class="flex items-center gap-2 transition-all duration-300" :class="hasSpecial ? 'text-[#007600]' : 'text-[#565959]'">
                                            <div class="w-4 h-4 rounded-full flex items-center justify-center border-2 transition-colors shrink-0" :class="hasSpecial ? 'bg-[#007600] border-[#007600]' : 'border-gray-300'">
                                                <i data-lucide="check" class="w-2.5 h-2.5 text-white" x-show="hasSpecial"></i>
                                            </div>
                                            <span class="text-xs font-medium">Contains special character</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="amazon-btn text-sm font-semibold px-6 py-2.5 rounded-lg border">
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
