@extends('layouts.seller')

@section('content')
@if(isset($profile) && $profile->seller_tier === 'local')
<div class="bg-gradient-to-r from-[#1a2a3a] to-[#0f1a26] rounded-2xl p-6 mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-lg font-bold text-white">Upgrade to Export Seller</h2>
        <p class="text-sm text-white/60 mt-1">Reach international buyers and grow your export business.</p>
    </div>
    <a href="{{ route('seller.onboarding.upgrade') }}" class="text-sm font-semibold px-6 py-2.5 rounded-lg border shrink-0 text-white bg-[#ffd814] border-[#fcd200] hover:bg-[#f7ca00]" style="color: #fff !important;">
        Upgrade Now
    </a>
</div>
@elseif(isset($profile) && $profile->seller_tier === 'export' && $profile->verification_status === 'pending')
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 flex items-start gap-4">
    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
        <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
    </div>
    <div>
        <h2 class="text-lg font-bold text-amber-800">Export Upgrade Under Review</h2>
        <p class="text-sm text-amber-700 mt-1">Your export seller upgrade has been submitted for verification. We will notify you once approved.</p>
    </div>
</div>
@endif

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Welcome Back, {{ $user->name }}</h1>
    <p class="text-slate-500 text-sm">Here's your seller overview for today.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center">
        <div class="p-3 bg-blue-100 rounded-2xl mr-4 text-blue-600">
            <i data-lucide="package" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase">Products</p>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($metrics['products']) }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center">
        <div class="p-3 bg-emerald-100 rounded-2xl mr-4 text-emerald-600">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase">Orders</p>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($metrics['orders']) }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center">
        <div class="p-3 bg-amber-100 rounded-2xl mr-4 text-amber-600">
            <i data-lucide="file-text" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase">Quotes</p>
            <p class="text-2xl font-bold text-slate-800">{{ number_format($metrics['quotes']) }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex items-center">
        <div class="p-3 bg-purple-100 rounded-2xl mr-4 text-purple-600">
            <i data-lucide="banknote" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase">Total Export Value</p>
            <p class="text-2xl font-bold text-slate-800">₦{{ number_format($metrics['export_value'], 2) }}</p>
        </div>
    </div>
</div>
@endsection
