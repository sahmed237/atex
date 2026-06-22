@extends('layouts.seller')

@section('title', 'My Products | Adamawa Ecommerce platform')
@section('header_title', 'My Products')

@section('content')
<section class="grid two" style="align-items: start;">
  <!-- Product Submission Form -->
  <div class="panel">
    <div class="panel-head">
      <h2>Submit New Product</h2>
      <span class="status pending">Draft</span>
    </div>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="margin-top: 20px; display: grid; gap: 15px;">
      @csrf
      <label>Product Name
        <input name="name" required placeholder="e.g. Cleaned Sesame Seed">
      </label>
      
      <label>Category
        <select name="category_id" required>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </label>

      <label class="wide">Description
        <textarea name="description" required placeholder="Detailed product specifications, moisture levels, quality indicators..."></textarea>
      </label>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>HS Code
          <input name="hs_code" placeholder="e.g. 120740">
        </label>
        <label>Minimum Order Qty (MOQ)
          <input name="moq" required placeholder="e.g. 10 MT">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>Available Quantity
          <input name="available_quantity" placeholder="e.g. 150 MT">
        </label>
        <label>Unit Price
          <input name="unit_price" placeholder="e.g. $1,200 / MT or leave empty">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>Origin LGA
          <input name="origin_lga" required placeholder="e.g. Ganye">
        </label>
        <label>Packaging
          <input name="packaging" placeholder="e.g. 50kg export sacks">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>Seller SKU
          <input name="seller_sku" placeholder="e.g. GANYE-SES-01">
        </label>
        <label>Brand Name
          <input name="brand_name" placeholder="e.g. Ganye Cooperatives">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>Fulfillment Model
          <select name="fulfillment_mode">
            <option value="seller_direct">Seller Direct</option>
            <option value="afribidge">AfriBridge Fulfillment</option>
          </select>
        </label>
        <label>Product Image
          <input type="file" name="product_image" accept="image/*">
        </label>
      </div>

      <div style="display: flex; gap: 20px; margin-top: 10px;">
        <label style="display: flex; flex-direction: row; align-items: center; gap: 8px; font-weight: normal;">
          <input type="checkbox" name="quote_required" value="1" checked style="width: auto; min-height: auto;">
          <span>Quote negotiation required</span>
        </label>
        <label style="display: flex; flex-direction: row; align-items: center; gap: 8px; font-weight: normal;">
          <input type="checkbox" name="fulfillment_eligible" value="1" style="width: auto; min-height: auto;">
          <span>Eligible for AfriBridge fulfillment</span>
        </label>
      </div>

      <button type="submit" class="btn primary full" style="margin-top: 10px;">Submit Product listing</button>
    </form>
  </div>

  <!-- Seller's Products List -->
  <div class="panel">
    <div class="panel-head">
      <h2>My Catalog Listings</h2>
      <span class="status active">{{ count($products) }} Listings</span>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
      <thead>
        <tr style="text-align: left; border-bottom: 1px solid var(--line);">
          <th style="padding: 10px 5px;">Product</th>
          <th style="padding: 10px 5px;">Category</th>
          <th style="padding: 10px 5px;">MOQ</th>
          <th style="padding: 10px 5px;">Price</th>
          <th style="padding: 10px 5px;">Readiness</th>
          <th style="padding: 10px 5px;">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr style="border-bottom: 1px solid var(--soft);">
            <td style="padding: 12px 5px;">
              <strong>{{ $product->name }}</strong><br>
              <small class="muted">SKU: {{ $product->seller_sku }}</small>
            </td>
            <td style="padding: 12px 5px;">{{ $product->category->name ?? '' }}</td>
            <td style="padding: 12px 5px;">{{ $product->moq }}</td>
            <td style="padding: 12px 5px;">{{ $product->unit_price }}</td>
            <td style="padding: 12px 5px;">{{ $product->readiness_score }}%</td>
            <td style="padding: 12px 5px;">
              <span class="status {{ $product->status }}">{{ str_replace('_', ' ', $product->status) }}</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="muted" style="padding: 20px; text-align: center;">No product listings found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
