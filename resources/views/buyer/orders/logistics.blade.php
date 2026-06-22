@extends('layouts.admin')

@section('title', 'Assigned Shipments | Adamawa Ecommerce platform')
@section('header_title', 'Assigned Shipments')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Cargo Assignments</h2>
    <span class="status active">{{ count($orders) }} Shipments</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Order No.</th>
        <th style="padding: 10px 5px;">Product</th>
        <th style="padding: 10px 5px;">Seller</th>
        <th style="padding: 10px 5px;">Destination</th>
        <th style="padding: 10px 5px;">Tracking Status</th>
        <th style="padding: 10px 5px; text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $order->order_number }}</strong><br>
            <small class="muted">Tracking: {{ $order->shipment->tracking_number ?? 'Awaiting assign' }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $order->product->name ?? 'Direct Cargo' }}</strong><br>
            <small class="muted">Qty: {{ $order->order_quantity }}</small>
          </td>
          <td style="padding: 12px 5px;">
            {{ $order->sellerProfile->business_name ?? 'Seller' }}
          </td>
          <td style="padding: 12px 5px;">
            {{ $order->destination_location }}
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $order->shipment->status ?? 'pending' }}">{{ str_replace('_', ' ', $order->shipment->status ?? 'pending') }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            <a class="btn secondary" href="{{ route('admin.orders.show', $order->id) }}" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal;">Update Status</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="muted" style="padding: 20px; text-align: center;">No shipments assigned to your company.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
