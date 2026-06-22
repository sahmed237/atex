@extends('layouts.admin')

@section('title', 'Product Management | Adamawa Ecommerce platform')
@section('header_title', 'Product Management')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Marketplace Products Queue</h2>
    <span class="status pending">Review Required</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Seller</th>
        <th style="padding: 10px 5px;">Product</th>
        <th style="padding: 10px 5px;">Category</th>
        <th style="padding: 10px 5px;">MOQ / Price</th>
        <th style="padding: 10px 5px;">Readiness</th>
        <th style="padding: 10px 5px;">Status</th>
        <th style="padding: 10px 5px; text-align: right;">Admin Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $product->sellerProfile->business_name ?? 'Unknown' }}</strong><br>
            <small class="muted">LGA: {{ $product->origin_lga }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $product->name }}</strong><br>
            <small class="muted">SKU: {{ $product->seller_sku }} | HS: {{ $product->hs_code ?: 'None' }}</small>
          </td>
          <td style="padding: 12px 5px;">{{ $product->category->name ?? '' }}</td>
          <td style="padding: 12px 5px;">
            MOQ: {{ $product->moq }}<br>
            Price: {{ $product->unit_price }}
          </td>
          <td style="padding: 12px 5px;">{{ $product->readiness_score }}%</td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $product->status }}">{{ str_replace('_', ' ', $product->status) }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            @if($product->status === 'pending_review')
              <div style="display: inline-flex; gap: 5px; justify-content: flex-end;">
                <form action="{{ route('admin.products.review', $product->id) }}" method="POST">
                  @csrf
                  <button type="submit" name="status" value="approved" class="btn primary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer; border: 0;">Approve</button>
                </form>
                <form action="{{ route('admin.products.review', $product->id) }}" method="POST">
                  @csrf
                  <button type="submit" name="status" value="rejected" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer;">Reject</button>
                </form>
              </div>
            @else
              <span class="muted">No Action</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="muted" style="padding: 20px; text-align: center;">No product listings found in queue.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
