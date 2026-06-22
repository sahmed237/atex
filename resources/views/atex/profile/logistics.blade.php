@extends('layouts.admin')

@section('title', 'My Profile | Adamawa Ecommerce platform')
@section('header_title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Logistics Profile</h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage your logistics business information</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold 
                {{ $profile->verification_status === 'approved' ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                {{ $profile->verification_status === 'pending' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : '' }}
                {{ $profile->verification_status === 'rejected' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}">
                <span class="w-1.5 h-1.5 rounded-full 
                    {{ $profile->verification_status === 'approved' ? 'bg-green-500' : '' }}
                    {{ $profile->verification_status === 'pending' ? 'bg-yellow-500' : '' }}
                    {{ $profile->verification_status === 'rejected' ? 'bg-red-500' : '' }}"></span>
                {{ ucfirst($profile->verification_status) }}
            </span>
        </div>

        <form action="{{ route('admin.profile.update') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-100">Account Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Contact Name</label>
                        <input name="name" value="{{ $user->name }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address</label>
                        <input value="{{ $user->email }}" disabled
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-500 bg-gray-50 outline-none cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                        <input name="phone" value="{{ $user->phone }}"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b border-gray-100">Business Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Company Name</label>
                        <input name="company_name" value="{{ $profile->company_name }}" required
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Base Location (HQ)</label>
                        <input name="base_location" value="{{ $profile->base_location }}" placeholder="e.g. Yola"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Coverage Regions</label>
                        <input name="coverage_regions" value="{{ $profile->coverage_regions }}" placeholder="e.g. Nigeria, Cameroon, Niger"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Transport Modes</label>
                        <input name="transport_modes" value="{{ $profile->transport_modes }}" placeholder="e.g. Road Freight, Air cargo"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Fleet Capacity Description</label>
                        <input name="fleet_capacity" value="{{ $profile->fleet_capacity }}" placeholder="e.g. 5 standard export cargo container vans"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#ffd814] hover:bg-[#f7ca00] text-gray-900 text-sm font-semibold rounded-lg border border-[#fcd200] transition-colors">
                    Save Changes
                </button>
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
