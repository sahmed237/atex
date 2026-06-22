@extends('layouts.admin')

@section('title', 'Seller Settlements | Adamawa Ecommerce platform')
@section('header_title', 'Seller Settlements')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Financial Settlements Ledger</h2>
    <span class="status active">{{ count($settlements) }} Logs</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Order No / Seller</th>
        <th style="padding: 10px 5px;">Gross</th>
        <th style="padding: 10px 5px;">Commission (10%)</th>
        <th style="padding: 10px 5px;">Export Tax (7.5%)</th>
        <th style="padding: 10px 5px;">Net Seller Payout</th>
        <th style="padding: 10px 5px;">Status</th>
        <th style="padding: 10px 5px;">Release Notes</th>
        <th style="padding: 10px 5px; text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($settlements as $set)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $set->order->order_number ?? 'AEM-ORD' }}</strong><br>
            <span class="muted">{{ $set->sellerProfile->business_name ?? 'Unknown Seller' }}</span>
          </td>
          <td style="padding: 12px 5px;">USD {{ number_format((float) $set->gross_amount, 2) }}</td>
          <td style="padding: 12px 5px; color: #b83a35;">- USD {{ number_format((float) $set->commission_amount, 2) }}</td>
          <td style="padding: 12px 5px; color: #b83a35;">- USD {{ number_format((float) $set->tax_amount, 2) }}</td>
          <td style="padding: 12px 5px; color: var(--leaf); font-weight: bold;">
            USD {{ number_format((float) $set->net_payout_amount, 2) }}
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $set->status }}">{{ $set->status }}</span>
          </td>
          <td style="padding: 12px 5px;">
            @if($set->status === 'credited')
              <small class="muted" style="font-style: italic;">"{{ $set->notes }}"</small><br>
              <small class="muted">Credited: {{ $set->credited_at ? $set->credited_at->format('M d, Y') : '' }}</small>
            @else
              <span class="muted">-</span>
            @endif
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            @if($set->status !== 'credited')
              <form action="{{ route('admin.settlements.credit') }}" method="POST" style="display: flex; gap: 4px; align-items: center; justify-content: flex-end;">
                @csrf
                <input type="hidden" name="settlement_id" value="{{ $set->id }}">
                <input name="notes" placeholder="Payout notes (optional)" style="min-height: 32px; font-size: 0.82rem; padding: 4px 8px; width: 140px;">
                <button type="submit" class="btn primary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; border: 0; cursor: pointer;">Release</button>
              </form>
            @else
              <span class="muted">Completed</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="muted" style="padding: 20px; text-align: center;">No settlements recorded in ledger.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
