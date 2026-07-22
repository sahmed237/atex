@extends('layouts.seller')

@section('title', 'Inventory | Adamawa Export Market')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Inventory Management</h1>
        <p class="text-slate-500 text-sm">Track and manage your export stock levels and AfriBridge warehouse deposits.</p>
    </div>
</div>

<!-- Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Lots Received</h3>
        <p class="text-2xl font-bold text-slate-800">{{ count($records) }}</p>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Received Qty</h3>
        <p class="text-2xl font-bold text-primary-600">
            {{ number_format($records->sum('quantity_received')) }}
        </p>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Available Qty</h3>
        <p class="text-2xl font-bold text-emerald-600">
            {{ number_format($records->sum('quantity_available')) }}
        </p>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Reserved Qty</h3>
        <p class="text-2xl font-bold text-amber-600">
            {{ number_format($records->sum('quantity_reserved')) }}
        </p>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-8 py-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
        <h2 class="text-lg font-bold text-slate-800">Warehouse Stock Inventory</h2>
        <span class="px-3 py-1 bg-primary-50 text-primary-600 rounded-full text-xs font-bold">{{ count($records) }} Entries</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/70 text-slate-500 text-xs font-bold uppercase tracking-wider">
                    <th class="px-8 py-4">Product Details</th>
                    <th class="px-6 py-4">Received</th>
                    <th class="px-6 py-4">Available</th>
                    <th class="px-6 py-4">Reserved</th>
                    <th class="px-6 py-4">Fulfilled</th>
                    <th class="px-6 py-4">Storage Location</th>
                    <th class="px-8 py-4">Date Received</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600 text-sm">
                @forelse($records as $record)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="font-bold text-slate-800">{{ $record->product->name ?? 'Unknown Product' }}</div>
                            <div class="text-xs text-slate-400 mt-1">SKU: {{ $record->seller_sku ?: 'N/A' }} | Brand: {{ $record->brand_name ?: 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-5 font-semibold text-slate-800">
                            {{ number_format($record->quantity_received) }} <span class="text-xs text-slate-400 font-normal">{{ $record->unit_label }}</span>
                        </td>
                        <td class="px-6 py-5 font-semibold text-emerald-600">
                            {{ number_format($record->quantity_available) }} <span class="text-xs text-slate-400 font-normal">{{ $record->unit_label }}</span>
                        </td>
                        <td class="px-6 py-5 font-semibold text-amber-600">
                            {{ number_format($record->quantity_reserved) }} <span class="text-xs text-slate-400 font-normal">{{ $record->unit_label }}</span>
                        </td>
                        <td class="px-6 py-5 font-semibold text-blue-600">
                            {{ number_format($record->quantity_fulfilled) }} <span class="text-xs text-slate-400 font-normal">{{ $record->unit_label }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $record->storage_location ?: 'N/A' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-slate-500 text-xs">
                            {{ $record->received_at ? $record->received_at->format('M d, Y H:i') : $record->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-8 py-12 text-center text-slate-400">
                            <div class="text-lg mb-1">No inventory records found</div>
                            <p class="text-sm">Once the platform administrator receives your stock lots, they will appear here.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
