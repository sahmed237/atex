@extends('layouts.admin')

@section('title', 'Request Quote | Adamawa Ecommerce platform')
@section('header_title', 'Request Quote')

@section('content')
<section class="panel" style="max-width: 600px; margin: 0 auto;">
  <div class="panel-head">
    <h3>Request Quote for {{ $product->name }}</h3>
    <span class="status pending">New RFQ</span>
  </div>
  <p class="muted" style="margin-top: 10px;">Submit your trade volume requirements. Seller will review your inquiries and respond with pricing and Incoterms details.</p>

  <form action="{{ route('admin.quotes.store') }}" method="POST" class="form-grid" style="margin-top: 25px; display: grid; gap: 15px;">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    
    <label>Product Listing
      <input type="text" value="{{ $product->name }}" disabled style="background: var(--soft);">
    </label>

    <label>Seller
      <input type="text" value="{{ $product->sellerProfile->business_name ?? 'Unknown Seller' }}" disabled style="background: var(--soft);">
    </label>

    <label>Required Quantity
      <input name="quantity" required placeholder="e.g. 20 MT or 10,000 Pcs">
    </label>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
      <label>Destination Country
        <input name="destination_country" required placeholder="e.g. UAE">
      </label>
      <label>Destination Port / Location
        <input name="destination_port" placeholder="e.g. Jebel Ali Port">
      </label>
    </div>

    <label>Incoterm
      <select name="incoterm" required>
        <option value="FOB">FOB (Free On Board)</option>
        <option value="CIF">CIF (Cost, Insurance & Freight)</option>
        <option value="EXW">EXW (Ex Works)</option>
      </select>
    </label>

    <label>Custom Message / Specifications
      <textarea name="message" placeholder="Include required inspection certificates (e.g. NAFDAC, organic), packaging specifications, or schedule parameters..."></textarea>
    </label>

    <button type="submit" class="btn primary full" style="margin-top: 10px; border: 0; cursor: pointer;">Send Quote Request (RFQ)</button>
  </form>
</section>
@endsection
