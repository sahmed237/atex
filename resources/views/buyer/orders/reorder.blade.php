@extends('layouts.buyer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('buyer.dashboard') }}" class="text-sm text-[#007185] hover:underline">&larr; Back to Dashboard</a>
        <h1 class="text-xl font-bold text-[#0f1111] mt-2">Reorder</h1>
        <p class="text-sm text-[#565959]">Order #{{ $reference }}</p>
    </div>

    <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center">
                <i data-lucide="rotate-ccw" class="w-5 h-5 text-[#007185]"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-[#0f1111]">Reorder Previous Purchase</h2>
                <p class="text-xs text-[#565959]">{{ $product_name }}</p>
            </div>
        </div>

        <div class="p-6 space-y-4">
            <div class="bg-[#f7f8f8] rounded-lg p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-[#0f1111]">{{ $product_name }}</p>
                    <p class="text-xs text-[#565959]">From your previous order #{{ $reference }}</p>
                </div>
                <span class="text-base font-bold text-[#0f1111]">&#8358;{{ number_format((float) $amount, 2) }}</span>
            </div>

            <div class="border-t border-[#e7e7e7] pt-4">
                <p class="text-sm text-[#565959]">This item is no longer in your cart. To place a new order, please visit the product page or browse our marketplace.</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('buyer.products.index') }}" class="amazon-btn text-sm font-semibold px-6 py-2.5 rounded-lg shadow-sm">
                    Browse Products
                </a>
                <a href="{{ route('buyer.dashboard') }}" class="text-sm text-[#565959] hover:underline">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
