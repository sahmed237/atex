@extends('layouts.admin')

@section('title', 'Fulfillment Operations | Adamawa Ecommerce platform')
@section('header_title', 'Fulfillment Operations')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>AfriBridge Fulfillment Queue</h2>
    <span class="status active">{{ count($orders) }} Active Shipments</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Order details</th>
        <th style="padding: 10px 5px;">Buyer / Seller</th>
        <th style="padding: 10px 5px;">Fulfillment Status</th>
        <th style="padding: 10px 5px;">Shipment Status</th>
        <th style="padding: 10px 5px; width: 380px; text-align: right;">Operations actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>Order #{{ $order->order_number }}</strong><br>
            <span class="muted">{{ $order->product->name ?? 'Direct Trade' }}</span><br>
            <small class="muted">Qty: {{ $order->order_quantity }} | Total: {{ $order->currency }} {{ number_format((float) $order->total_amount, 2) }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <small>Buyer: {{ $order->buyerProfile->company_name ?? 'Buyer Account' }}</small><br>
            <small>Seller: {{ $order->sellerProfile->business_name ?? 'Seller' }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $order->fulfillment_status }}">{{ $order->fulfillment_status }}</span>
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $order->shipment_status }}">{{ str_replace('_', ' ', $order->shipment_status) }}</span><br>
            @if($order->shipment && $order->shipment->logistics_profile_id)
              <small class="muted">Partner: {{ $order->shipment->logisticsProfile->company_name ?? '' }}</small>
            @endif
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
              <!-- 1. Update Fulfillment Status Form -->
              <form action="{{ route('admin.fulfillment.update', $order->id) }}" method="POST" style="display: flex; gap: 4px; align-items: center;">
                @csrf
                <select name="fulfillment_status" style="min-height: 32px; font-size: 0.85rem; padding: 4px 8px; width: auto;">
                  <option value="pending" {{ $order->fulfillment_status === 'pending' ? 'selected' : '' }}>Fulfillment: Pending</option>
                  <option value="processing" {{ $order->fulfillment_status === 'processing' ? 'selected' : '' }}>Fulfillment: Processing</option>
                  <option value="dispatched" {{ $order->fulfillment_status === 'dispatched' ? 'selected' : '' }}>Fulfillment: Dispatched</option>
                  <option value="fulfilled" {{ $order->fulfillment_status === 'fulfilled' ? 'selected' : '' }}>Fulfillment: Completed</option>
                </select>
                <select name="shipment_status" style="min-height: 32px; font-size: 0.85rem; padding: 4px 8px; width: auto;">
                  <option value="pending_assignment" {{ $order->shipment_status === 'pending_assignment' ? 'selected' : '' }}>Shipment: Pending Assign</option>
                  <option value="picked_up" {{ $order->shipment_status === 'picked_up' ? 'selected' : '' }}>Shipment: Picked Up</option>
                  <option value="customs_cleared" {{ $order->shipment_status === 'customs_cleared' ? 'selected' : '' }}>Shipment: Customs Cleared</option>
                  <option value="departed_origin" {{ $order->shipment_status === 'departed_origin' ? 'selected' : '' }}>Shipment: Departed</option>
                  <option value="in_transit" {{ $order->shipment_status === 'in_transit' ? 'selected' : '' }}>Shipment: In Transit</option>
                  <option value="delivered" {{ $order->shipment_status === 'delivered' ? 'selected' : '' }}>Shipment: Delivered</option>
                </select>
                <input type="hidden" name="status" value="{{ $order->status }}">
                <button type="submit" class="btn primary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; border: 0; cursor: pointer;">Update</button>
              </form>

              <!-- 2. Assign Logistics Partner Form -->
              <form action="{{ route('admin.shipment.assign') }}" method="POST" style="display: flex; gap: 4px; align-items: center;">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="status" value="pickup_scheduled">
                <input type="hidden" name="order_shipment_status" value="picked_up">
                
                <select name="logistics_profile_id" required style="min-height: 32px; font-size: 0.85rem; padding: 4px 8px; width: auto;">
                  <option value="">Assign Logistics...</option>
                  @foreach($logistics as $partner)
                    <option value="{{ $partner->id }}" {{ $order->shipment && $order->shipment->logistics_profile_id === $partner->id ? 'selected' : '' }}>{{ $partner->company_name }}</option>
                  @endforeach
                </select>
                <input name="tracking_number" placeholder="Tracking No." value="{{ $order->shipment->tracking_number ?? '' }}" required style="min-height: 32px; font-size: 0.85rem; padding: 4px 8px; width: 110px;">
                <button type="submit" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer;">Assign</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="muted" style="padding: 20px; text-align: center;">No AfriBridge fulfillment orders found.</td>
        </tr>
      @forelse
    </tbody>
  </table>
</section>
@endsection
