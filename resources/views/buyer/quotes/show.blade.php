@extends('layouts.admin')

@section('title', 'RFQ Details | Adamawa Ecommerce platform')
@section('header_title', 'RFQ Details')

@section('content')
<section class="grid two" style="align-items: start;">
  <!-- RFQ Specs Panel -->
  <div class="panel">
    <div class="panel-head">
      <h3>RFQ Specifications</h3>
      <span class="status {{ $quote->status }}">{{ $quote->status }}</span>
    </div>
    
    <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
      <tbody>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px; width: 150px;">Buyer</th>
          <td style="padding: 10px 5px;">
            <strong>{{ $quote->buyerProfile->company_name ?? 'Buyer Account' }}</strong><br>
            <small class="muted">{{ $quote->buyerProfile->user->name ?? '' }} | {{ $quote->buyerProfile->user->email ?? '' }}</small>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Product</th>
          <td style="padding: 10px 5px;">
            <strong>{{ $quote->product->name ?? 'Deleted Product' }}</strong><br>
            <small class="muted">HS Code: {{ $quote->product->hs_code ?: 'None' }}</small>
          </td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Quantity</th>
          <td style="padding: 10px 5px;">{{ $quote->quantity }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Destination</th>
          <td style="padding: 10px 5px;">{{ $quote->destination_country }} (Port: {{ $quote->destination_port ?: 'None' }})</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Incoterm</th>
          <td style="padding: 10px 5px;">{{ $quote->incoterm }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--soft);">
          <th style="text-align: left; padding: 10px 5px;">Message</th>
          <td style="padding: 10px 5px; white-space: pre-line;">{{ $quote->message ?: 'No message attached.' }}</td>
        </tr>
        <tr>
          <th style="text-align: left; padding: 10px 5px;">Requested</th>
          <td style="padding: 10px 5px;">{{ $quote->created_at->format('M d, Y H:i') }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Action Response Panel -->
  <div class="panel">
    @if($quote->status === 'open' && $user->hasRole('seller'))
      <!-- Seller response form -->
      <div class="panel-head">
        <h3>Submit Quote Offer</h3>
        <span class="status pending">Required</span>
      </div>
      <form action="{{ route('admin.quotes.respond', $quote->id) }}" method="POST" class="form-grid" style="margin-top: 20px; display: grid; gap: 15px;">
        @csrf
        <label>Response Quote Amount (USD)
          <input type="text" name="response_amount" required placeholder="e.g. 24000">
        </label>
        <label>Response / Offer Details
          <textarea name="response_message" required placeholder="e.g. We can fulfill 20 MT sesame in 50kg sacks FOB Lagos. Inspection certificates attached."></textarea>
        </label>
        <button type="submit" class="btn primary full">Submit Quote Offer</button>
      </form>
    
    @elseif($quote->status === 'responded')
      <!-- Display Response Offer details -->
      <div class="panel-head">
        <h3>Seller Offer</h3>
        <span class="status active">Active Offer</span>
      </div>
      <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
        <tbody>
          <tr style="border-bottom: 1px solid var(--soft);">
            <th style="text-align: left; padding: 10px 5px; width: 150px;">Offer Amount</th>
            <td style="padding: 10px 5px;">
              <strong style="color: var(--leaf); font-size: 1.25rem;">USD {{ number_format((float) $quote->response_amount, 2) }}</strong>
            </td>
          </tr>
          <tr style="border-bottom: 1px solid var(--soft);">
            <th style="text-align: left; padding: 10px 5px;">Offer Details</th>
            <td style="padding: 10px 5px; white-space: pre-line;">{{ $quote->response_message }}</td>
          </tr>
          <tr>
            <th style="text-align: left; padding: 10px 5px;">Responded At</th>
            <td style="padding: 10px 5px;">{{ $quote->responded_at ? $quote->responded_at->format('M d, Y H:i') : '' }}</td>
          </tr>
        </tbody>
      </table>

      @if($user->hasRole('buyer'))
        <!-- Convert quote to purchase order -->
        <div style="margin-top: 25px; padding: 15px; background: #eef5f0; border-radius: 8px;">
          <h4 style="margin-top: 0; color: #0f5132;">Accept Offer & Purchase</h4>
          <p class="muted" style="font-size: 0.88rem; margin-bottom: 15px;">Accepting this quote generates an official purchase order. Funds will be held in escrow until logistics validation.</p>
          <form action="{{ route('admin.orders.store') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $quote->product_id }}">
            <input type="hidden" name="order_quantity" value="{{ $quote->quantity }}">
            <input type="hidden" name="destination_location" value="{{ $quote->destination_port ?: $quote->destination_country }}">
            <input type="hidden" name="total_amount" value="{{ $quote->response_amount }}">
            <input type="hidden" name="currency" value="USD">
            <button type="submit" class="btn primary full" style="border: 0; cursor: pointer;">Place Secure Order (Escrow)</button>
          </form>
        </div>
      @endif

    @else
      <!-- Just view status -->
      <div class="panel-head">
        <h3>Negotiation Status</h3>
        <span class="status {{ $quote->status }}">{{ $quote->status }}</span>
      </div>
      <p style="margin-top: 15px;">Awaiting response from seller. Standard SLA for response is 24 hours.</p>
    @endif
  </div>
</section>
@endsection
