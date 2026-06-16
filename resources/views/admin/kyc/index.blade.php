@extends('layouts.admin')

@section('title', 'KYC Verification | Adamawa Export Market')
@section('header_title', 'KYC verification')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>KYC Registration Verification</h2>
    <span class="status pending">Awaiting Audit</span>
  </div>
  
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Applicant Profile</th>
        <th style="padding: 10px 5px;">Type</th>
        <th style="padding: 10px 5px;">Sector / Location</th>
        <th style="padding: 10px 5px;">Verification Status</th>
        <th style="padding: 10px 5px;">Account Status</th>
        <th style="padding: 10px 5px; text-align: right; width: 320px;">Verification Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($profiles as $profile)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ $profile['organization'] }}</strong><br>
            <small class="muted">{{ $profile['name'] }} ({{ $profile['email'] }})</small>
          </td>
          <td style="padding: 12px 5px;">
            {{ $profile['profile_type_label'] ?? ucfirst($profile['profile_type']) }}<br>
            <small class="muted">RC: {{ $profile['rc_number'] }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <div style="margin-bottom: 4px;">BVN: <strong>{{ $profile['bvn'] }}</strong></div>
            <div>NIN: <strong>{{ $profile['nin'] }}</strong></div>
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $profile['verification_status'] }}">{{ $profile['verification_status'] }}</span><br>
            <div style="margin-top: 5px; font-size: 0.8rem;">
              @foreach($profile['documents'] as $doc)
                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank" style="display: block; color: var(--primary);">📄 {{ $doc->title }}</a>
              @endforeach
            </div>
          </td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $profile['account_status'] }}">{{ $profile['account_status'] }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            <form action="{{ route('admin.kyc.review') }}" method="POST" style="display: inline-flex; gap: 5px; justify-content: flex-end;">
              @csrf
              <input type="hidden" name="profile_type" value="{{ $profile['profile_type'] }}">
              <input type="hidden" name="profile_id" value="{{ $profile['id'] }}">
              
              <a href="{{ route('admin.kyc.show', ['type' => $profile['profile_type'], 'id' => $profile['id']]) }}" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;">View Details</a>
              
              @if($profile['verification_status'] !== 'approved')
                <button type="submit" name="status" value="approved" class="btn primary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; border: 0; cursor: pointer;">Verify</button>
              @endif
              @if($profile['verification_status'] !== 'rejected')
                <button type="submit" name="status" value="rejected" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer;">Reject</button>
              @endif
              @if($profile['verification_status'] !== 'pending')
                <button type="submit" name="status" value="pending" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer;">Hold</button>
              @endif
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="muted" style="padding: 20px; text-align: center;">No profiles registered in queue.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
