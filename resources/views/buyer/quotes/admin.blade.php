@extends('layouts.admin')

@section('title', 'RFQs Management | Adamawa Ecommerce platform')
@section('header_title', 'Request For Quotes')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Global RFQ Logs</h2>
    <span class="status active">{{ count($quotes) }} Quotes</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Buyer</th>
        <th style="padding: 10px 5px;">Product</th>
        <th style="padding: 10px 5px;">Quantity</th>
        <th style="padding: 10px 5px;">Destination</th>
        <th style="padding: 10px 5px;">Incoterm</th>
        <th style="padding: 10px 5px;">Status</th>
        <th style="padding: 10px 5px; text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($quotes as $quote)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $quote->buyerProfile->company_name ?? 'Buyer Account' }}</strong>
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $quote->product->name ?? 'Deleted Product' }}</strong><br>
            <small class="muted">Seller: {{ $quote->product->sellerProfile->business_name ?? 'Unknown' }}</small>
          </td>
          <td style="padding: 12px 5px;">{{ $quote->quantity }}</td>
          <td style="padding: 12px 5px;">{{ $quote->destination_country }}</td>
          <td style="padding: 12px 5px;">{{ $quote->incoterm }}</td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $quote->status }}">{{ $quote->status }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            <a class="btn secondary" href="{{ route('admin.quotes.show', $quote->id) }}" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal;">View Details</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="muted" style="padding: 20px; text-align: center;">No RFQs logged in system.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
