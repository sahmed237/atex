@extends('layouts.admin')

@section('title', 'Warehouse Inventory | Adamawa Ecommerce platform')
@section('header_title', 'Warehouse Inventory')

@section('content')
<section class="grid two" style="align-items: start;">
  <!-- Record Inventory Arrival -->
  <div class="panel">
    <div class="panel-head">
      <h2>Record Stored Inventory Lot</h2>
      <span class="status pending">Intake</span>
    </div>
    <form action="{{ route('admin.inventory.receive') }}" method="POST" class="form-grid" style="margin-top: 20px; display: grid; gap: 15px;">
      @csrf
      <label>Seller Profile
        <select name="seller_profile_id" required>
          @foreach($sellers as $exp)
            <option value="{{ $exp->id }}">{{ $exp->business_name }}</option>
          @endforeach
        </select>
      </label>

      <label>Product Listing
        <select name="product_id" required>
          @foreach($products as $prod)
            <option value="{{ $prod->id }}">{{ $prod->name }} (by {{ $prod->sellerProfile->business_name ?? 'Unknown' }})</option>
          @endforeach
        </select>
      </label>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;">
        <label>Quantity Stored
          <input type="number" name="quantity_received" required placeholder="e.g. 50">
        </label>
        <label>Unit Label
          <input name="unit_label" value="units" required placeholder="e.g. units, MT, bags">
        </label>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <label>Brand Name (optional)
          <input name="brand_name" placeholder="e.g. Ganye Gold">
        </label>
        <label>Seller SKU (optional)
          <input name="seller_sku" placeholder="e.g. SKU-GANYE-SES">
        </label>
      </div>

      <label>Warehouse Storage Location
        <input name="storage_location" required value="AfriBridge Warehouse Yola" placeholder="e.g. AfriBridge Warehouse Yola">
      </label>

      <label>Lot Notes
        <textarea name="notes" placeholder="Specify package conditions, weight checks, or moisture inspection values..."></textarea>
      </label>

      <button type="submit" class="btn primary full" style="margin-top: 10px; border: 0; cursor: pointer;">Record Inventory Arrival</button>
    </form>
  </div>

  <!-- Stored Lots Listing -->
  <div class="panel">
    <div class="panel-head">
      <h2>Warehouse Stored Stocks</h2>
      <span class="status active">{{ count($records) }} Lots</span>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
      <thead>
        <tr style="text-align: left; border-bottom: 1px solid var(--line);">
          <th style="padding: 10px 5px;">Seller / Product</th>
          <th style="padding: 10px 5px;">Stock Level</th>
          <th style="padding: 10px 5px;">Location</th>
          <th style="padding: 10px 5px;">Arrival</th>
        </tr>
      </thead>
      <tbody>
        @forelse($records as $rec)
          <tr style="border-bottom: 1px solid var(--soft);">
            <td style="padding: 12px 5px;">
              <strong>{{ $rec->sellerProfile->business_name ?? 'Unknown' }}</strong><br>
              <span class="muted">{{ $rec->product->name ?? 'Deleted product' }}</span><br>
              <small class="muted">SKU: {{ $rec->seller_sku ?: 'AEM-SKU' }} | Brand: {{ $rec->brand_name ?: 'AEM' }}</small>
            </td>
            <td style="padding: 12px 5px;">
              <strong>{{ $rec->quantity_available }}</strong> / {{ $rec->quantity_received }} {{ $rec->unit_label }}<br>
              <span class="status {{ $rec->receipt_status }}">{{ $rec->receipt_status }}</span>
            </td>
            <td style="padding: 12px 5px;">
              {{ $rec->storage_location }}<br>
              @if($rec->notes)
                <small class="muted" style="display: block; font-style: italic;">"{{ $rec->notes }}"</small>
              @endif
            </td>
            <td style="padding: 12px 5px;">
              <small>{{ $rec->received_at ? $rec->received_at->format('M d, Y') : $rec->created_at->format('M d, Y') }}</small>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="muted" style="padding: 20px; text-align: center;">No stored inventory recorded.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
