@extends('layouts.buyer')

@section('content')
<div class="bg-white rounded-lg border border-[#e7e7e7] p-6 mb-6">
    <a href="{{ route('buyer.products.index') }}" class="link-blue text-sm flex items-center gap-1 mb-4">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
        Back to results
    </a>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div class="aspect-square bg-[#f7f7f7] rounded-lg flex items-center justify-center p-8">
            @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
            @else
                <i data-lucide="package" class="w-24 h-24 text-gray-300"></i>
            @endif
        </div>

        <!-- Product Details -->
        <div>
            <p class="text-sm text-[#565959] mb-1">{{ $product->brand_name ?: ($product->sellerProfile->business_name ?? 'ATEX Marketplace') }}</p>
            <h1 class="text-xl font-bold text-[#0f1111] leading-snug mb-2">{{ $product->name }}</h1>

            <div class="flex items-center gap-3 mb-3">
                <div class="flex text-[#de7921] text-sm">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                <a href="#" class="link-blue text-sm">{{ rand(5, 50) }} ratings</a>
            </div>

            <hr class="border-gray-200 mb-4">

            @if($product->unit_price && $product->unit_price !== 'Request quote')
            <div class="mb-4">
                <span class="text-2xl font-bold text-[#0f1111]">&#8358;{{ number_format((float) $product->unit_price) }}</span>
            </div>
            @else
            <div class="mb-4">
                <span class="text-lg font-bold text-[#007185]">Price: Request Quote</span>
            </div>
            @endif

            <div class="space-y-2 text-sm mb-4">
                <div class="flex gap-2">
                    <span class="text-[#565959] w-28 shrink-0">MOQ:</span>
                    <span class="font-medium">{{ $product->moq }}</span>
                </div>
                @if($product->hs_code)
                <div class="flex gap-2">
                    <span class="text-[#565959] w-28 shrink-0">HS Code:</span>
                    <span class="font-medium">{{ $product->hs_code }}</span>
                </div>
                @endif
                @if($product->origin_lga)
                <div class="flex gap-2">
                    <span class="text-[#565959] w-28 shrink-0">Origin:</span>
                    <span class="font-medium">{{ $product->origin_lga }}</span>
                </div>
                @endif
                @if($product->packaging)
                <div class="flex gap-2">
                    <span class="text-[#565959] w-28 shrink-0">Packaging:</span>
                    <span class="font-medium">{{ $product->packaging }}</span>
                </div>
                @endif
            </div>

            <hr class="border-gray-200 mb-4">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="store" class="w-5 h-5 text-[#565959]"></i>
                </div>
                <div>
                    <p class="text-xs text-[#565959]">Sold by</p>
                    <p class="text-sm font-bold text-[#0f1111]">{{ $product->sellerProfile->business_name ?? 'ATEX Seller' }}</p>
                </div>
            </div>

            @if($product->description)
            <hr class="border-gray-200 mb-4">
            <div>
                <h3 class="text-sm font-bold text-[#0f1111] mb-1">About this product</h3>
                <p class="text-sm text-[#0f1111] leading-relaxed">{{ $product->description }}</p>
            </div>
            @endif

            <div class="mt-6 flex gap-3">
                <form action="{{ route('admin.quotes.create') }}" method="GET">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button type="submit" class="amazon-btn text-sm font-semibold px-8 py-3 rounded-full border">
                        <i data-lucide="message-square" class="w-4 h-4 inline mr-1"></i>
                        Inquire
                    </button>
                </form>
                <button class="amazon-btn-secondary text-sm font-semibold px-8 py-3 rounded-full border">
                    <i data-lucide="shopping-cart" class="w-4 h-4 inline mr-1"></i>
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

@if($related->count() > 0)
<div class="mb-8">
    <h2 class="section-title mb-4">Related Products</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($related as $item)
        <div class="product-card rounded-lg overflow-hidden">
            <a href="{{ route('buyer.products.show', $item->id) }}" class="aspect-square bg-white flex items-center justify-center p-4 block">
                @if($item->image_path)
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-contain">
                @else
                    <div class="w-full h-full bg-[#f7f7f7] flex items-center justify-center">
                        <i data-lucide="package" class="w-10 h-10 text-gray-300"></i>
                    </div>
                @endif
            </a>
            <div class="p-3">
                <p class="text-[11px] text-[#565959] mb-0.5 truncate">{{ $item->brand_name ?: ($item->sellerProfile->business_name ?? 'ATEX') }}</p>
                <a href="{{ route('buyer.products.show', $item->id) }}" class="link-blue text-sm leading-snug line-clamp-2 mb-1">{{ $item->name }}</a>
                <div class="flex text-[#de7921] text-xs mb-1">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                @if($item->unit_price && $item->unit_price !== 'Request quote')
                    <span class="text-base font-bold text-[#0f1111]">&#8358;{{ number_format((float) $item->unit_price) }}</span>
                @else
                    <span class="text-sm font-bold text-[#007185]">Request Quote</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
