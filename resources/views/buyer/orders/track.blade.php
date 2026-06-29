@extends('layouts.buyer')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('buyer.dashboard') }}" class="text-sm text-[#007185] hover:underline">&larr; Back to Dashboard</a>
        <h1 class="text-xl font-bold text-[#0f1111] mt-2">Track Order</h1>
        <p class="text-sm text-[#565959]">Order #{{ $tracking['order_number'] }}</p>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden mb-4 shadow-sm">
        <div class="px-6 py-5 border-b border-[#e7e7e7] flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#f0f2f2] flex items-center justify-center">
                    <i data-lucide="package" class="w-5 h-5 text-[#007185]"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0f1111]">{{ $tracking['product_name'] }}</h2>
                    <p class="text-xs text-[#565959]">Qty: {{ $tracking['quantity'] }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border"
                  style="color: {{ $tracking['status'] === 'Delivered' ? '#007600' : ($tracking['status'] === 'Processing' ? '#c45500' : '#007185') }};
                         border-color: {{ $tracking['status'] === 'Delivered' ? '#007600' : ($tracking['status'] === 'Processing' ? '#c45500' : '#007185') }};
                         background: {{ $tracking['status'] === 'Delivered' ? '#f0fff0' : ($tracking['status'] === 'Processing' ? '#fff5f0' : '#f0f7ff') }};">
                {{ $tracking['status'] }}
            </span>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs text-[#565959] font-semibold mb-1">Origin</p>
                <p class="text-sm text-[#0f1111]">{{ $tracking['origin'] }}</p>
            </div>
            <div>
                <p class="text-xs text-[#565959] font-semibold mb-1">Destination</p>
                <p class="text-sm text-[#0f1111]">{{ $tracking['destination'] }}</p>
            </div>
            <div>
                <p class="text-xs text-[#565959] font-semibold mb-1">Tracking Number</p>
                <p class="text-sm font-bold text-[#0f1111]">{{ $tracking['tracking_number'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white rounded-lg border border-[#e7e7e7] p-4">
            <p class="text-xs text-[#565959] font-semibold mb-1">Logistics Partner</p>
            <p class="text-sm font-bold text-[#0f1111]">{{ $tracking['logistics_partner'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-[#e7e7e7] p-4">
            <p class="text-xs text-[#565959] font-semibold mb-1">Payment Status</p>
            <p class="text-sm font-bold text-[#0f1111] capitalize">{{ $tracking['payment_status'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-[#e7e7e7] p-4">
            <p class="text-xs text-[#565959] font-semibold mb-1">Order Date</p>
            <p class="text-sm font-bold text-[#0f1111]">{{ $tracking['created_at'] instanceof \Carbon\Carbon ? $tracking['created_at']->format('M d, Y') : date('M d, Y', strtotime($tracking['created_at'])) }}</p>
        </div>
    </div>

    <!-- Horizontal Animated Progress Bar -->
    <div class="bg-white rounded-lg border border-[#e7e7e7] p-6 mb-4 shadow-sm">
        <h3 class="text-xs font-bold text-[#565959] uppercase tracking-wider mb-6 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
            Live Transit Progress
        </h3>
        <div class="relative flex items-center justify-between max-w-3xl mx-auto px-4">
            <!-- Connecting Line -->
            <div class="absolute left-8 right-8 top-1/2 -translate-y-1/2 h-1.5 bg-gray-100 z-0"></div>
            @php
                $progressPct = $tracking['status'] === 'Delivered' ? 100 : ($tracking['status'] === 'Processing' ? 33 : 66);
            @endphp
            <div class="absolute left-8 top-1/2 -translate-y-1/2 h-1.5 bg-gradient-to-r from-blue-600 to-green-600 transition-all duration-1000 z-0" style="width: calc({{ $progressPct }}% - 4rem);"></div>

            <!-- Steps -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-md font-bold text-sm">✓</div>
                <span class="text-xs font-bold text-[#0f1111] mt-2">Origin Adamawa</span>
            </div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full {{ $progressPct >= 33 ? 'bg-blue-600 text-white shadow-md animate-bounce' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold text-sm">
                    {{ $progressPct >= 66 ? '✓' : '🏛️' }}
                </div>
                <span class="text-xs font-bold text-[#0f1111] mt-2">Customs Cleared</span>
            </div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full {{ $progressPct >= 66 ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold text-sm">
                    {{ $progressPct >= 100 ? '✓' : '🚢' }}
                </div>
                <span class="text-xs font-bold text-[#0f1111] mt-2">In Transit</span>
            </div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full {{ $progressPct >= 100 ? 'bg-green-600 text-white shadow-lg' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center font-bold text-sm">🎉</div>
                <span class="text-xs font-bold text-[#0f1111] mt-2">Delivered</span>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-lg border border-[#e7e7e7] overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-[#e7e7e7]">
            <h2 class="text-sm font-bold text-[#0f1111] flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-[#007185]"></i>
                Shipment Timeline
            </h2>
        </div>
        <div class="p-6">
            <div class="relative">
                <div class="absolute left-[17px] top-2 bottom-2 w-0.5 bg-gray-200"></div>
                @foreach($tracking['timeline'] as $i => $event)
                    @php
                        $isLast = $loop->last;
                        $isPast = $loop->iteration <= $loop->count - ($tracking['status'] === 'Delivered' ? 0 : 1);
                    @endphp
                    <div class="flex gap-4 pb-8 relative {{ $isLast ? 'pb-0' : '' }}">
                        <div class="relative z-10 shrink-0">
                            @if($isPast)
                                <div class="w-9 h-9 rounded-full bg-[#007600] flex items-center justify-center">
                                    <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center">
                                    <i data-lucide="circle" class="w-3 h-3 text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 pt-1">
                            <p class="text-sm font-bold text-[#0f1111]">{{ $event['status'] }}</p>
                            <p class="text-xs text-[#565959] mt-0.5">{{ $event['description'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $event['date'] instanceof \Carbon\Carbon ? $event['date']->format('M d, Y H:i') : date('M d, Y H:i', strtotime($event['date'])) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
