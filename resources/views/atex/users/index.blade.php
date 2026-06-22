@extends('layouts.admin')

@section('title', 'User Administration | Adamawa Ecommerce platform')
@section('header_title', 'User Administration')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Registered Trade System Accounts</h2>
    <span class="status active">{{ count($accounts) }} Accounts</span>
  </div>
  
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">User</th>
        <th style="padding: 10px 5px;">Primary Role</th>
        <th style="padding: 10px 5px;">Linked Organization</th>
        <th style="padding: 10px 5px;">Registered</th>
        <th style="padding: 10px 5px;">Status</th>
        <th style="padding: 10px 5px; text-align: right; width: 280px;">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($accounts as $account)
        @php
          $org = '-';
          if ($account->sellerProfile) {
              $org = $account->sellerProfile->business_name;
          } elseif ($account->buyerProfile) {
              $org = $account->buyerProfile->company_name ?: 'Buyer Account';
          } elseif ($account->logisticsProfile) {
              $org = $account->logisticsProfile->company_name;
          }
        @endphp
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $account->name }}</strong><br>
            <small class="muted">{{ $account->email }}</small><br>
            @if($account->phone)
              <small class="muted">Phone: {{ $account->phone }}</small>
            @endif
          </td>
          <td style="padding: 12px 5px;">
            @foreach($account->roles as $role)
              <code style="background: var(--soft); padding: 2px 4px; border-radius: 4px; color: var(--deep); font-size: 0.82rem;">{{ $role->name }}</code>
            @endforeach
          </td>
          <td style="padding: 12px 5px;">{{ $org }}</td>
          <td style="padding: 12px 5px;">{{ $account->created_at->format('M d, Y') }}</td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $account->status }}">{{ $account->status }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            @if(Auth::id() !== $account->id)
              <form action="{{ route('admin.atex.users.status') }}" method="POST" style="display: inline-flex; gap: 5px; justify-content: flex-end;">
                @csrf
                <input type="hidden" name="user_id" value="{{ $account->id }}">
                @if($account->status !== 'active')
                  <button type="submit" name="status" value="active" class="btn primary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; border: 0; cursor: pointer;">Approve</button>
                @endif
                @if($account->status !== 'suspended')
                  <button type="submit" name="status" value="suspended" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer;">Suspend</button>
                @endif
              </form>
            @else
              <span class="muted">Self</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="muted" style="padding: 20px; text-align: center;">No system user accounts found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
