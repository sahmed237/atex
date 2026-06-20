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
                            <a href="#" class="link-blue text-xs font-semibold">Track</a>
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
                            <a href="#" class="link-blue text-xs font-semibold">Review</a>
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
                            <a href="#" class="link-blue text-xs font-semibold">Reorder</a>
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

<div class="flex items-center justify-between mb-3">
    <h2 class="section-title flex items-center gap-2">
        <i data-lucide="trending-up" class="w-5 h-5 text-[#007185]"></i>
        Recommended for You
    </h2>
    <a href="{{ route('buyer.products.index') }}" class="link-blue text-sm font-semibold">See all &rarr;</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    @for($i = 0; $i < 5; $i++)
    <div class="product-card rounded-lg overflow-hidden">
        <div class="aspect-square bg-white flex items-center justify-center p-4">
            <div class="w-full h-full bg-[#f7f7f7] rounded flex items-center justify-center">
                <i data-lucide="package" class="w-10 h-10 text-gray-300"></i>
            </div>
        </div>
        <div class="p-3">
            <p class="text-[11px] text-[#565959] mb-0.5 truncate">Featured Supplier</p>
            <p class="link-blue text-sm leading-snug line-clamp-2 mb-1">Premium Export Product from Nigeria</p>
            <div class="flex text-[#de7921] text-xs mb-1">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
            <span class="text-base font-bold text-[#0f1111]">&#8358;0.00</span>
            <button class="w-full amazon-btn text-[13px] font-semibold py-1.5 rounded-full border mt-2">Inquire</button>
        </div>
    </div>
    @endfor
</div>
@endsection
