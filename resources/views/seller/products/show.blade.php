@extends('layouts.seller')

@section('title', $product->name . ' - Product Details | Adamawa Export Market')
@section('header_title', 'Product Catalog Details')

@section('content')
<div class="mb-6">
  <a href="{{ route('seller.catalog.index') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--primary, #2563eb); text-decoration: none; font-weight: bold; font-size: 0.9rem;">
    ← Back to Catalog List
  </a>
</div>

<section class="grid two" style="align-items: start; gap: 25px;">
  <!-- Product Specification Details -->
  <div class="panel">
    <div class="panel-head" style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h2 style="margin: 0; font-size: 1.5rem; font-weight: bold; color: var(--text-dark, #1e293b);">{{ $product->name }}</h2>
        <span class="muted" style="font-size: 0.85rem;">SKU: {{ $product->seller_sku }}</span>
      </div>
      <span class="status {{ $product->status }}" style="padding: 6px 14px; border-radius: 9999px; font-weight: bold; text-transform: uppercase; font-size: 0.75rem;">
        {{ str_replace('_', ' ', $product->status) }}
      </span>
    </div>

    <div style="margin-top: 25px; display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; border-top: 1px solid var(--line, #e2e8f0); padding-top: 20px;">
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Category</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->category->name ?? 'None' }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Brand Name</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->brand_name ?: 'None' }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">HS Code</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155); font-family: monospace;">{{ $product->hs_code ?: 'N/A' }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Origin LGA</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->origin_lga }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Minimum Order Qty (MOQ)</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->moq }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Available Quantity</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->available_quantity ?: 'None' }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Unit Price</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155); font-weight: bold;">{{ $product->unit_price }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Fulfillment Model</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155); text-transform: capitalize;">{{ str_replace('_', ' ', $product->fulfillment_model) }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Packaging</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->packaging }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Weight</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">{{ $product->weight ? $product->weight . ' kg' : 'N/A' }}</span>
      </div>
      <div>
        <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase;">Dimensions (L×W×H)</strong>
        <span style="font-size: 1rem; color: var(--text-dark, #334155);">
          @if($product->length || $product->width || $product->height)
            {{ $product->length ?: 0 }} × {{ $product->width ?: 0 }} × {{ $product->height ?: 0 }} cm
          @else
            N/A
          @endif
        </span>
      </div>
    </div>

    <div style="margin-top: 25px; border-top: 1px solid var(--line, #e2e8f0); padding-top: 20px;">
      <strong style="display: block; font-size: 0.8rem; color: var(--text-muted, #64748b); text-transform: uppercase; margin-bottom: 8px;">Description</strong>
      <p style="font-size: 0.95rem; color: var(--text-dark, #475569); line-height: 1.6; margin: 0; background: var(--soft, #f8fafc); padding: 15px; border-radius: 8px; border: 1px solid var(--line, #f1f5f9);">
        {{ $product->description }}
      </p>
    </div>
  </div>

  <!-- Compliance & Media Panel -->
  <div style="display: grid; gap: 25px;">
    <!-- Product Image -->
    @if($product->image_path)
      <div class="panel">
        <div class="panel-head">
          <h3>Product Image</h3>
        </div>
        <div style="margin-top: 15px; display: flex; justify-content: center; background: var(--soft, #f8fafc); padding: 20px; border-radius: 8px; border: 1px solid var(--line, #f1f5f9);">
          <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 250px; border-radius: 6px; object-fit: contain;">
        </div>
      </div>
    @endif

    <!-- Product Readiness Panel -->
    <div class="panel">
      <div class="panel-head">
        <h3>Product Export Readiness</h3>
      </div>
      <div style="margin-top: 15px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
          <span style="font-weight: bold; color: var(--text-dark, #334155);">Score</span>
          <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary, #2563eb);">{{ $product->readiness_score }}%</span>
        </div>
        <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 9999px; overflow: hidden;">
          <div style="width: {{ $product->readiness_score }}%; height: 100%; background: var(--primary, #2563eb); border-radius: 9999px;"></div>
        </div>
        <ul style="margin-top: 20px; padding: 0; list-style: none; display: grid; gap: 10px; font-size: 0.85rem; color: var(--text-dark, #475569);">
          <li style="display: flex; align-items: center; gap: 8px;">
            <span style="color: {{ $product->hs_code ? '#10b981' : '#cbd5e1' }}; font-weight: bold;">✔</span> HS Code assigned
          </li>
          <li style="display: flex; align-items: center; gap: 8px;">
            <span style="color: {{ $product->image_path ? '#10b981' : '#cbd5e1' }}; font-weight: bold;">✔</span> Product imagery uploaded
          </li>
          <li style="display: flex; align-items: center; gap: 8px;">
            <span style="color: {{ $product->fulfillment_eligible ? '#10b981' : '#cbd5e1' }}; font-weight: bold;">✔</span> Eligible for AfriBridge central fulfillment
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Linked Warehouse Stock Lots -->
<section class="panel" style="margin-top: 25px;">
  <div class="panel-head">
    <h2>Warehouse Stock Inventory Lots</h2>
    <span class="status active" style="font-size: 0.8rem;">{{ count($inventoryLots) }} Lots</span>
  </div>
  <p class="muted" style="font-size: 0.85rem; margin-top: 5px;">This displays physical stock deposits of this product received and stored at the AfriBridge warehouse.</p>
  
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line, #e2e8f0); color: var(--text-muted, #64748b); font-size: 0.85rem;">
        <th style="padding: 10px 5px;">Lot ID / SKU</th>
        <th style="padding: 10px 5px;">Received</th>
        <th style="padding: 10px 5px;">Available</th>
        <th style="padding: 10px 5px;">Reserved</th>
        <th style="padding: 10px 5px;">Fulfilled</th>
        <th style="padding: 10px 5px;">Location</th>
        <th style="padding: 10px 5px;">Received Date</th>
      </tr>
    </thead>
    <tbody>
      @forelse($inventoryLots as $lot)
        <tr style="border-bottom: 1px solid var(--soft, #f1f5f9); font-size: 0.9rem; color: var(--text-dark, #334155);">
          <td style="padding: 12px 5px;">
            <strong>Lot #{{ $lot->id }}</strong><br>
            <small class="muted" style="font-family: monospace;">SKU: {{ $lot->seller_sku ?: 'N/A' }}</small>
          </td>
          <td style="padding: 12px 5px;">{{ number_format($lot->quantity_received) }} <span class="muted" style="font-size: 0.8rem;">{{ $lot->unit_label }}</span></td>
          <td style="padding: 12px 5px; color: #10b981; font-weight: bold;">{{ number_format($lot->quantity_available) }} <span class="muted" style="font-size: 0.8rem;">{{ $lot->unit_label }}</span></td>
          <td style="padding: 12px 5px; color: #f59e0b; font-weight: bold;">{{ number_format($lot->quantity_reserved) }} <span class="muted" style="font-size: 0.8rem;">{{ $lot->unit_label }}</span></td>
          <td style="padding: 12px 5px; color: #3b82f6; font-weight: bold;">{{ number_format($lot->quantity_fulfilled) }} <span class="muted" style="font-size: 0.8rem;">{{ $lot->unit_label }}</span></td>
          <td style="padding: 12px 5px;">
            <span style="background: var(--soft, #f8fafc); border: 1px solid var(--line, #e2e8f0); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;">
              {{ $lot->storage_location ?: 'Warehouse' }}
            </span>
          </td>
          <td style="padding: 12px 5px; font-size: 0.8rem;" class="muted">
            {{ $lot->received_at ? $lot->received_at->format('M d, Y H:i') : $lot->created_at->format('M d, Y H:i') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="muted" style="padding: 30px; text-align: center; font-size: 0.9rem;">
            No warehouse stock lots recorded for this product yet.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
