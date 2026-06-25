@extends('layouts.buyer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('buyer.dashboard') }}" class="text-sm text-[#007185] hover:underline">&larr; Back to Dashboard</a>
        <h1 class="text-xl font-bold text-[#0f1111] mt-2">Review Order</h1>
        <p class="text-sm text-[#565959]">Order #{{ $reference }}</p>
    </div>

    <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-[#e7e7e7] flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center">
                <i data-lucide="package" class="w-5 h-5 text-[#007185]"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-[#0f1111]">{{ $product_name }}</h2>
                <p class="text-xs text-[#565959]">Order #{{ $reference }}</p>
            </div>
        </div>

        <form action="{{ route('buyer.orders.review.store', $reference) }}" method="POST" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="product_name" value="{{ $product_name }}">

            <div>
                <label class="block text-sm font-bold text-[#0f1111] mb-3">Rate your experience</label>
                <div class="flex gap-2" x-data="{ rating: 0 }">
                    <template x-for="star in 5" :key="star">
                        <button type="button" @click="rating = star" class="p-1 transition-colors">
                            <svg :class="star <= rating ? 'text-[#de7921]' : 'text-gray-300'" class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                    </template>
                    <input type="hidden" name="rating" x-model="rating" required>
                </div>
                @error('rating')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="comment" class="block text-sm font-bold text-[#0f1111] mb-2">Write your review (optional)</label>
                <textarea name="comment" id="comment" rows="4"
                          class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm text-[#0f1111] focus:border-[#007185] focus:ring-1 focus:ring-[#007185] outline-none transition-colors"
                          placeholder="Share your experience with this product...">{{ old('comment') }}</textarea>
                @error('comment')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="amazon-btn text-sm font-semibold px-6 py-2.5 rounded-lg shadow-sm">
                    Submit Review
                </button>
                <a href="{{ route('buyer.dashboard') }}" class="text-sm text-[#565959] hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
