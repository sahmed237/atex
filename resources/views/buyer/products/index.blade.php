@extends('layouts.buyer')

@section('content')
<!-- Hero Banner -->
<div class="bg-gradient-to-br from-[#1a2a3a] to-[#0f1a26] rounded-xl overflow-hidden mb-6 relative">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50"></div>
    <div class="relative z-10 p-6 md:p-10 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1 text-center md:text-left">
            <span class="text-[#febd69] text-xs font-bold uppercase tracking-widest">Welcome to ATEX</span>
            <h1 class="text-2xl md:text-3xl font-bold text-white mt-2 leading-tight">Adamawa Export<br>Marketplace</h1>
            <p class="text-white/60 text-sm mt-2 max-w-lg">Source premium agricultural products, textiles, and handicrafts directly from verified exporters in Nigeria.</p>
        </div>
        <div class="flex gap-3">
            <a href="#" class="amazon-btn text-sm font-semibold px-6 py-2.5 rounded-full">Shop Now</a>
            <a href="{{ route('seller.onboarding') }}" class="border border-white/30 text-white text-sm font-semibold px-6 py-2.5 rounded-full hover:bg-white/10 transition-colors">Start Selling</a>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="bg-white rounded-lg border border-[#e7e7e7] p-3 mb-4">
    <form method="GET" action="{{ route('buyer.products.index') }}" class="flex items-center gap-2">
        <div class="flex-1 min-w-[200px] relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                   class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none">
        </div>
        <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-[#007185] outline-none">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-[#007185] outline-none">
            <option value="">Sort: Newest</option>
            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
        </select>
        <button type="submit" class="bg-[#007185] text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-[#005f6b] transition-colors">Go</button>
    </form>
</div>

@if($products->count() > 0)
<!-- Results info -->
<div class="flex items-center justify-between mb-3">
    <p class="text-sm text-[#565959]">
        <span class="font-bold text-[#0f1111]">{{ $products->total() }}</span> results
        @if(request('search'))
            for "<span class="font-bold">{{ request('search') }}</span>"
        @endif
    </p>
</div>

<!-- Product Grid -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
    @foreach($products as $product)
    <div class="product-card rounded-lg overflow-hidden flex flex-col">
        <!-- Image -->
        <a href="{{ route('buyer.products.show', $product->id) }}" class="aspect-square bg-white flex items-center justify-center p-4 relative">
            @if($product->image_path)
                <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
            @else
                <div class="w-full h-full bg-[#f7f7f7] flex items-center justify-center">
                    <i data-lucide="package" class="w-12 h-12 text-gray-300"></i>
                </div>
            @endif
        </a>
        <!-- Info -->
        <div class="p-3 flex flex-col flex-1">
            <p class="text-[11px] text-[#565959] mb-0.5 truncate">{{ $product->brand_name ?: ($product->sellerProfile->business_name ?? 'ATEX') }}</p>
            <a href="{{ route('buyer.products.show', $product->id) }}" class="link-blue text-sm leading-snold mb-1 line-clamp-2" style="font-size: 0.875rem; line-height: 1.2;">{{ $product->name }}</a>
            <!-- Rating -->
            <div class="flex items-center gap-1.5 mb-1">
                <div class="flex text-[#de7921] text-xs">
                    &#9733;&#9733;&#9733;&#9733;&#9734;
                </div>
                <span class="text-[11px] text-[#007185]">{{ rand(5, 50) }}</span>
            </div>
            <!-- Price -->
            <div class="mt-auto">
                @if($product->unit_price && $product->unit_price !== 'Request quote')
                    <div class="flex items-baseline gap-0.5">
                        <span class="price-symbol text-xs font-semibold">&#8358;</span>
                        <span class="text-lg font-bold text-[#0f1111]">{{ number_format((float) $product->unit_price) }}</span>
                    </div>
                @else
                    <span class="text-sm font-bold text-[#007185]">Request Quote</span>
                @endif
                <p class="text-[11px] text-[#565959] mt-0.5">MOQ: {{ $product->moq }}</p>
            </div>
            <!-- Button -->
            <form action="{{ route('admin.quotes.create') }}" method="GET" class="mt-2">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="w-full amazon-btn text-[13px] font-semibold py-1.5 rounded-full border">
                    Inquire
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="mt-6 flex justify-center">
    {{ $products->links() }}
</div>

@else
<!-- Empty State -->
<div class="text-center py-20">
    <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
        <i data-lucide="package" class="w-10 h-10 text-gray-400"></i>
    </div>
    <h3 class="text-xl font-bold text-[#0f1111] mb-2">No products found</h3>
    <p class="text-[#565959] text-sm max-w-md mx-auto">
        @if(request('search') || request('category'))
            Try adjusting your search or filter criteria.
        @else
            No products are available yet. Check back soon!
        @endif
    </p>
    @if(request('search') || request('category'))
        <a href="{{ route('buyer.products.index') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 bg-[#007185] text-white text-sm font-semibold rounded-lg hover:bg-[#005f6b] transition-colors">
            Clear Filters
        </a>
    @endif
</div>
@endif
@endsection
