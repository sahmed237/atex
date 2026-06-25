@extends('layouts.buyer')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg border border-[#e7e7e7] p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center shrink-0">
            <i data-lucide="shopping-bag" class="w-5 h-5 text-[#007185]"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-[#0f1111]">{{ number_format($metrics['total_orders']) }}</p>
            <p class="text-xs text-[#565959]">Total Orders</p>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-[#e7e7e7] p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center shrink-0">
            <i data-lucide="message-square" class="w-5 h-5 text-[#007185]"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-[#0f1111]">{{ number_format($metrics['active_rfqs']) }}</p>
            <p class="text-xs text-[#565959]">Active RFQs</p>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-[#e7e7e7] p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center shrink-0">
            <i data-lucide="heart" class="w-5 h-5 text-[#007185]"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-[#0f1111]">{{ number_format($metrics['saved_items']) }}</p>
            <p class="text-xs text-[#565959]">Saved Items</p>
        </div>
    </div>
    <div class="bg-white rounded-lg border border-[#e7e7e7] p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center shrink-0">
            <i data-lucide="credit-card" class="w-5 h-5 text-[#007185]"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-[#0f1111]">&#8358;{{ number_format($metrics['total_spent'], 2) }}</p>
            <p class="text-xs text-[#565959]">Total Spent</p>
        </div>
    </div>
</div>

@if(!$user->hasRole('seller'))
    @if($sellerProfile && $sellerProfile->verification_status === 'pending')
    <div class="bg-[#fff8e1] border border-[#ffb300] rounded-lg px-5 py-4 mb-6 flex items-start gap-3">
        <i data-lucide="clock" class="w-5 h-5 text-[#ff8f00] shrink-0 mt-0.5"></i>
        <div>
            <h3 class="font-bold text-[#0f1111]">Seller Application Under Review</h3>
            <p class="text-sm text-[#565959] mt-0.5">Your application to become a seller is being reviewed. You'll be notified once approved.</p>
        </div>
    </div>
    @elseif(!$sellerProfile || $sellerProfile->verification_status !== 'approved')
    <div class="bg-white rounded-lg border border-[#e7e7e7] p-5 mb-6 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-[#0f1111]">Start selling on ATEX</h3>
            <p class="text-sm text-[#565959] mt-0.5">Reach buyers across Nigeria and beyond.</p>
        </div>
        <a href="{{ route('seller.onboarding') }}" class="amazon-btn text-sm font-semibold px-6 py-2 rounded-full border shrink-0">
            Become a Seller
        </a>
    </div>
    @endif
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2">
        <h2 class="section-title mb-3 flex items-center gap-2">
            <i data-lucide="clock" class="w-5 h-5 text-[#007185]"></i>
            Recent Orders
        </h2>
        <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] text-[#565959] font-bold uppercase border-b border-[#e7e7e7]">
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0f2f2]">
                    <tr class="hover:bg-[#f7f8f8] transition-colors">
                        <td class="px-4 py-3 font-semibold text-[#0f1111]">#ORD-12345</td>
                        <td class="px-4 py-3 text-[#565959]">Today</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-[#c45500]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#c45500]"></span>
                                Processing
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold">&#8358;45,000</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('buyer.orders.track', 'ORD-12345') }}" class="link-blue text-xs font-semibold">Track</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#f7f8f8] transition-colors">
                        <td class="px-4 py-3 font-semibold text-[#0f1111]">#ORD-12344</td>
                        <td class="px-4 py-3 text-[#565959]">Yesterday</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-[#007600]">
                                <i data-lucide="check-circle" class="w-3 h-3"></i>
                                Delivered
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold">&#8358;120,500</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('buyer.orders.review', 'ORD-12344') }}" class="link-blue text-xs font-semibold">Review</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-[#f7f8f8] transition-colors">
                        <td class="px-4 py-3 font-semibold text-[#0f1111]">#ORD-12343</td>
                        <td class="px-4 py-3 text-[#565959]">Oct 12, 2023</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-[#565959]">
                                <i data-lucide="x" class="w-3 h-3"></i>
                                Cancelled
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold">&#8358;15,000</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('buyer.orders.reorder', 'ORD-12343') }}" class="link-blue text-xs font-semibold">Reorder</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="section-title mb-3 flex items-center gap-2">
            <i data-lucide="star" class="w-5 h-5 text-[#de7921]"></i>
            Quick Links
        </h2>
        <div class="bg-white rounded-lg border border-[#e7e7e7] divide-y divide-[#f0f2f2]">
            <a href="{{ route('buyer.products.index') }}" class="flex items-center gap-3 px-4 py-3.5 hover:bg-[#f7f8f8] transition-colors group">
                <i data-lucide="search" class="w-5 h-5 text-[#007185]"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-[#0f1111]">Browse Products</p>
                    <p class="text-xs text-[#565959]">Explore our marketplace</p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3.5 hover:bg-[#f7f8f8] transition-colors group">
                <i data-lucide="heart" class="w-5 h-5 text-[#007185]"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-[#0f1111]">Wishlist</p>
                    <p class="text-xs text-[#565959]">{{ $metrics['saved_items'] }} saved items</p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
            </a>
            <a href="#" class="flex items-center gap-3 px-4 py-3.5 hover:bg-[#f7f8f8] transition-colors group">
                <i data-lucide="package" class="w-5 h-5 text-[#007185]"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-[#0f1111]">Track Shipments</p>
                    <p class="text-xs text-[#565959]">Monitor deliveries</p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
            </a>
            <a href="{{ route('buyer.profile.show') }}" class="flex items-center gap-3 px-4 py-3.5 hover:bg-[#f7f8f8] transition-colors group">
                <i data-lucide="user" class="w-5 h-5 text-[#007185]"></i>
                <div class="flex-1">
                    <p class="text-sm font-medium text-[#0f1111]">My Profile</p>
                    <p class="text-xs text-[#565959]">Account settings</p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300"></i>
            </a>
        </div>
    </div>
</div>
@endsection
