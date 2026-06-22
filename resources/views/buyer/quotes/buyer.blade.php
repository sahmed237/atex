@extends('layouts.admin')

@section('title', 'My RFQs | Adamawa Ecommerce platform')
@section('header_title', 'My Quote Requests')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Sent Inquiries</h2>
    <span class="status active">{{ count($quotes) }} Requests</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Seller</th>
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
            <strong>{{ $quote->product->sellerProfile->business_name ?? 'Unknown' }}</strong>
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $quote->product->name ?? 'Deleted Product' }}</strong>
          </td>
          <td style="padding: 12px 5px;">{{ $quote->quantity }}</td>
          <td style="padding: 12px 5px;">{{ $quote->destination_country }}</td>
          <td style="padding: 12px 5px;">{{ $quote->incoterm }}</td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $quote->status }}">{{ $quote->status }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            <a class="btn secondary" href="{{ route('admin.quotes.show', $quote->id) }}" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal;">
              @if($quote->status === 'responded')
                View Quote & Buy
              @else
                View Details
              @endif
            </a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="muted" style="padding: 20px; text-align: center;">No RFQs submitted yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
