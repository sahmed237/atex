@extends('layouts.admin')

@section('title', 'Place Secure Order | Adamawa Ecommerce platform')
@section('header_title', 'Place Secure Order')

@section('content')
<section class="panel" style="max-width: 600px; margin: 0 auto;">
  <div class="panel-head">
    <h3>Direct Purchase: {{ $product->name }}</h3>
    <span class="status pending">Secure checkout</span>
  </div>
  <p class="muted" style="margin-top: 10px;">Specify your trade volume requirements and enter the agreed transaction price. Escrow holds the funds secure until delivery verification.</p>

  <form action="{{ route('admin.orders.store') }}" method="POST" class="form-grid" style="margin-top: 25px; display: grid; gap: 15px;">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    
    <label>Product Listing
      <input type="text" value="{{ $product->name }}" disabled style="background: var(--soft);">
    </label>

    <label>Seller / Seller
      <input type="text" value="{{ $product->sellerProfile->business_name ?? 'Unknown Seller' }}" disabled style="background: var(--soft);">
    </label>

    <label>MOQ / Price Info
      <input type="text" value="MOQ: {{ $product->moq }} | Price: {{ $product->unit_price }}" disabled style="background: var(--soft);">
    </label>

    <label>Order Quantity
      <input name="order_quantity" required placeholder="e.g. 15 MT or 20,000 Units" value="{{ $product->moq }}">
    </label>

    <label>Destination Location Address
      <input name="destination_location" required placeholder="e.g. Port of Rotterdam, Netherlands">
    </label>

    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 10px;">
      <label>Agreed Total Payout Amount
        <input type="number" step="0.01" name="total_amount" required placeholder="e.g. 18500">
      </label>
      <label>Currency
        <input name="currency" value="USD" readonly style="background: var(--soft);">
      </label>
    </div>

    <button type="submit" class="btn primary full" style="margin-top: 10px; border: 0; cursor: pointer;">Place Order in Escrow</button>
  </form>
</section>
@endsection
