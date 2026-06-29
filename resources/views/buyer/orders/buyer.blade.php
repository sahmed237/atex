@extends('layouts.admin')

@section('title', 'My Orders | Adamawa Ecommerce platform')
@section('header_title', 'My Purchases')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>My Purchases</h2>
    <span class="status active">{{ count($orders) }} Purchases</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Order No.</th>
        <th style="padding: 10px 5px;">Product</th>
        <th style="padding: 10px 5px;">Seller</th>
        <th style="padding: 10px 5px;">Amount</th>
        <th style="padding: 10px 5px;">Payment Status</th>
        <th style="padding: 10px 5px;">Shipment</th>
        <th style="padding: 10px 5px; text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $order->order_number }}</strong><br>
            <small class="muted">{{ $order->created_at->format('M d, Y') }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $order->product->name ?? 'Direct Purchase' }}</strong><br>
            <small class="muted">Qty: {{ $order->order_quantity }}</small>
          </td>
          <td style="padding: 12px 5px;">
            {{ $order->sellerProfile->business_name ?? 'Seller' }}
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $order->currency }} {{ number_format((float) $order->total_amount, 2) }}</strong>
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $order->payment_status }}">{{ $order->payment_status }}</span>
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $order->shipment_status }}">{{ str_replace('_', ' ', $order->shipment_status) }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            <a class="btn secondary" href="{{ route('buyer.orders.track', $order->order_number) }}" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal;">Track Shipment</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="muted" style="padding: 20px; text-align: center;">No purchases placed yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
