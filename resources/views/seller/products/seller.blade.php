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
    <form action="{{ route('seller.catalog.store') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="margin-top: 20px; display: grid; gap: 15px;">
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
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px; align-items: end;">
          <label style="margin: 0;">Minimum Order Qty (MOQ)
            <input type="number" step="any" name="moq_value" required placeholder="e.g. 10">
          </label>
          <label style="margin: 0;">MOQ Unit
            <select name="moq_unit" required style="width: 100%; height: 38px;">
              @foreach($units as $unit)
                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
              @endforeach
            </select>
          </label>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px; align-items: end;">
          <label style="margin: 0;">Available Quantity
            <input type="number" step="any" name="available_quantity_value" placeholder="e.g. 150">
          </label>
          <label style="margin: 0;">Qty Unit
            <select name="available_quantity_unit" style="width: 100%; height: 38px;">
              @foreach($units as $unit)
                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
              @endforeach
            </select>
          </label>
        </div>
        <label>Unit Price
          <input name="unit_price" placeholder="e.g. $1,200 / MT or leave empty">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px;">
        <label>Weight (kg)
          <input type="number" step="any" name="weight" placeholder="e.g. 50">
        </label>
        <label>Length (cm)
          <input type="number" step="any" name="length" placeholder="Length">
        </label>
        <label>Width (cm)
          <input type="number" step="any" name="width" placeholder="Width">
        </label>
        <label>Height (cm)
          <input type="number" step="any" name="height" placeholder="Height">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>Origin LGA
          <input name="origin_lga" required placeholder="e.g. Ganye">
        </label>
        <label>Packaging
          <select name="packaging" required style="width: 100%; height: 38px;">
            @foreach($packagings as $pkg)
              <option value="{{ $pkg->name }}">{{ $pkg->name }}</option>
            @endforeach
          </select>
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
        @if(isset($profile) && $profile->seller_tier === 'export')
          <div style="padding:6px 14px; background:#f3e8ff; border-radius:6px; font-size:0.85rem; color:#7c3aed; font-weight:600;">🌍 Export item — Quotes required</div>
        @else
          <input type="hidden" name="quote_required" value="0">
        @endif
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
              <a href="{{ route('seller.catalog.show', $product->id) }}" style="color: var(--primary, #2563eb); text-decoration: none; font-weight: bold; hover:underline;">
                {{ $product->name }}
              </a><br>
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
