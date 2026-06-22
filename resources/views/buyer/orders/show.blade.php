@extends('layouts.admin')

@section('title', 'Order Details | Adamawa Ecommerce platform')
@section('header_title', 'Order Details')

@section('content')
<section class="grid two" style="align-items: start;">
  <!-- Order Breakdown -->
  <div class="panel">
    <div class="panel-head">
      <h3>Order #{{ $order->order_number }}</h3>
      <span class="status {{ $order->status }}">{{ str_replace('_', ' ', $order->status) }}</span>
    </div>
    
    <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
      <tbody>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px; width: 150px;">Product</th>
          <td style="padding: 10px 5px;">
            <strong>{{ $order->product->name ?? 'Direct Trade Lot' }}</strong><br>
            <small class="muted">SKU: {{ $order->product->seller_sku ?? 'AEM' }}</small>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Quantity</th>
          <td style="padding: 10px 5px;">{{ $order->order_quantity }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Fulfillment</th>
          <td style="padding: 10px 5px;">{{ str_replace('_', ' ', $order->fulfillment_mode) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Escrow Payment</th>
          <td style="padding: 10px 5px;">
            <span class="status {{ $order->payment_status }}">{{ $order->payment_status }}</span>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Buyer Account</th>
          <td style="padding: 10px 5px;">
            <strong>{{ $order->buyerProfile->company_name ?? 'Buyer LLC' }}</strong><br>
            <small class="muted">Destination: {{ $order->destination_location }}</small>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Seller</th>
          <td style="padding: 10px 5px;">
            <strong>{{ $order->sellerProfile->business_name ?? 'Seller Cooperative' }}</strong>
          </td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 10px 5px;">Date Placed</th>
          <td style="padding: 10px 5px;">{{ $order->created_at->format('M d, Y H:i') }}</td>
        </tr>
      </tbody>
    </table>

    <div style="margin-top: 25px; padding: 15px; background: var(--soft); border-radius: 8px;">
      <h4 style="margin-top: 0;">Escrow & Payout Breakdown</h4>
      <table style="width: 100%; font-size: 0.9rem; margin-top: 10px; border-collapse: collapse;">
        <tbody>
          <tr>
            <td style="padding: 4px 0;">Gross Trade Value:</td>
            <td style="text-align: right; padding: 4px 0;">{{ $order->currency }} {{ number_format((float) $order->total_amount, 2) }}</td>
          </tr>
          <tr>
            <td style="padding: 4px 0; color: #b83a35;">Platform Commission (10%):</td>
            <td style="text-align: right; padding: 4px 0; color: #b83a35;">- {{ $order->currency }} {{ number_format((float) $order->commission_amount, 2) }}</td>
          </tr>
          <tr>
            <td style="padding: 4px 0; color: #b83a35;">State Export Tax (7.5%):</td>
            <td style="text-align: right; padding: 4px 0; color: #b83a35;">- {{ $order->currency }} {{ number_format((float) $order->tax_amount, 2) }}</td>
          </tr>
          <tr style="border-top: 1px solid var(--line); font-weight: bold;">
            <td style="padding: 8px 0; color: var(--leaf);">Net Seller Payout:</td>
            <td style="text-align: right; padding: 8px 0; color: var(--leaf);">{{ $order->currency }} {{ number_format((float) $order->net_payout_amount, 2) }}</td>
          </tr>
        </tbody>
      </table>
      <div style="margin-top: 10px; font-size: 0.82rem;" class="muted">
        Settlement Status: <strong class="status {{ $order->settlement_status }}">{{ $order->settlement_status }}</strong>
      </div>
    </div>
  </div>

  <!-- Logistics & Timeline Panel -->
  <div class="panel">
    @php
      $shipStatus = $order->shipment_status;
      $step1 = 'done';
      $step2 = $order->payment_status !== 'pending' ? 'done' : 'pending';
      $step3 = in_array($shipStatus, ['customs_cleared', 'departed_origin', 'in_transit', 'delivered']) ? 'done' : ($step2 === 'done' ? 'current' : 'pending');
      $step4 = in_array($shipStatus, ['departed_origin', 'in_transit', 'delivered']) ? 'done' : ($step3 === 'done' ? 'current' : 'pending');
      $step5 = $shipStatus === 'delivered' ? 'done' : ($step4 === 'done' ? 'current' : 'pending');
    @endphp

    <div class="panel-head">
      <h3>Order Shipment Timeline</h3>
      <span class="status {{ $shipStatus }}">{{ str_replace('_', ' ', $shipStatus) }}</span>
    </div>

    <ol class="timeline" style="margin-top: 20px;">
      <li class="{{ $step1 }}">
        <span></span>
        <div><strong>Quote Accepted & Order Created</strong><small>{{ $order->created_at->format('M d, Y') }}</small></div>
      </li>
      <li class="{{ $step2 }}">
        <span></span>
        <div><strong>Payment Confirmed</strong><small>Escrow hold active (Status: {{ $order->payment_status }})</small></div>
      </li>
      <li class="{{ $step3 }}">
        <span></span>
        <div><strong>Customs Cleared</strong><small>Phytosanitary and export certificate approved</small></div>
      </li>
      <li class="{{ $step4 }}">
        <span></span>
        <div><strong>Departed Origin / In Transit</strong><small>Cargo handoff to shipping lanes</small></div>
      </li>
      <li class="{{ $step5 }}">
        <span></span>
        <div><strong>Arrival & Inspection</strong><small>Awaiting final port scan</small></div>
      </li>
    </ol>

    @if($order->shipment && $order->shipment->logistics_profile_id)
      <div style="margin-top: 20px; padding: 12px; background: var(--soft); border-radius: 8px; font-size: 0.9rem;">
        <strong>Logistics Partner:</strong> {{ $order->shipment->logisticsProfile->company_name ?? 'Sahel Freight' }}<br>
        <strong>Tracking Number:</strong> {{ $order->shipment->tracking_number ?: 'Awaiting assignment' }}<br>
        <strong>Milestone:</strong> {{ $order->shipment->notes ?: 'Handoff scheduled.' }}
      </div>
    @endif

    @if($user->hasRole('logistics') && $order->shipment && $order->shipment->logistics_profile_id)
      <!-- Logistics status update form -->
      <div style="margin-top: 25px; border-top: 1px solid var(--line); padding-top: 20px;">
        <h4>Update Tracking Milestones</h4>
        <form action="{{ route('admin.shipment.update', $order->shipment->id) }}" method="POST" class="form-grid" style="margin-top: 15px; display: grid; gap: 12px;">
          @csrf
          <label>Tracking Number
            <input name="tracking_number" value="{{ $order->shipment->tracking_number }}" required>
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <label>Cargo Milestone Status
              <select name="status" required>
                <option value="pickup_scheduled" {{ $order->shipment->status === 'pickup_scheduled' ? 'selected' : '' }}>Pickup Scheduled</option>
                <option value="picked_up" {{ $order->shipment->status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="customs_cleared" {{ $order->shipment->status === 'customs_cleared' ? 'selected' : '' }}>Customs Cleared</option>
                <option value="departed" {{ $order->shipment->status === 'departed' ? 'selected' : '' }}>Departed Origin</option>
                <option value="in_transit" {{ $order->shipment->status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ $order->shipment->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
              </select>
            </label>
            <label>Overall Shipment Status
              <select name="order_shipment_status" required>
                <option value="pending" {{ $order->shipment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="picked_up" {{ $order->shipment_status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                <option value="customs_cleared" {{ $order->shipment_status === 'customs_cleared' ? 'selected' : '' }}>Customs Cleared</option>
                <option value="departed_origin" {{ $order->shipment_status === 'departed_origin' ? 'selected' : '' }}>Departed Origin</option>
                <option value="in_transit" {{ $order->shipment_status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ $order->shipment_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
              </select>
            </label>
          </div>
          <label>Milestone Log Note
            <textarea name="notes" placeholder="e.g. Cleared Ganye custom gates, moving to port.">{{ $order->shipment->notes }}</textarea>
          </label>
          <button type="submit" class="btn primary full" style="border: 0; cursor: pointer;">Update Tracking Status</button>
        </form>
      </div>
    @endif
  </div>
</section>
@endsection
