@extends('layouts.admin')

@section('title', 'Document Compliance | Adamawa Ecommerce platform')
@section('header_title', 'Document Compliance')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Compliance Verification Queue</h2>
    <span class="status pending">Audit Queued</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px;">Applicant Profile</th>
        <th style="padding: 10px 5px;">Document details</th>
        <th style="padding: 10px 5px;">Type</th>
        <th style="padding: 10px 5px;">Expiry</th>
        <th style="padding: 10px 5px;">Status</th>
        <th style="padding: 10px 5px; text-align: right; width: 320px;">Verification Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($documents as $doc)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">
            <strong>{{ ucfirst($doc->owner_type) }} (ID: {{ $doc->owner_id }})</strong><br>
            <small class="muted">Uploaded: {{ $doc->created_at->format('M d, Y') }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <strong>{{ $doc->title }}</strong><br>
            @if($doc->path)
              <a href="{{ asset($doc->path) }}" target="_blank" class="muted" style="text-decoration: underline; font-size: 0.85rem;">View Document Scan</a>
            @endif
            @if($doc->review_comment)
              <div style="font-size: 0.8rem; margin-top: 5px; color: #66716b; background: var(--soft); padding: 4px; border-left: 2px solid var(--line);">
                <strong>Comment:</strong> {{ $doc->review_comment }} (by {{ $doc->reviewer->name ?? 'System' }})
              </div>
            @endif
          </td>
          <td style="padding: 12px 5px;">{{ str_replace('_', ' ', $doc->document_type) }}</td>
          <td style="padding: 12px 5px;">{{ $doc->expiry_date ? $doc->expiry_date->format('M d, Y') : 'Permanent' }}</td>
          <td style="padding: 12px 5px;">
            <span class="status {{ $doc->status }}">{{ $doc->status }}</span>
          </td>
          <td style="padding: 12px 5px; text-align: right;">
            @if($doc->status === 'pending')
              <form action="{{ route('admin.documents.review', $doc->id) }}" method="POST" style="display: flex; gap: 5px; flex-direction: column; align-items: flex-end;">
                @csrf
                <input name="comment" placeholder="Review comment (optional/req for reject)" style="min-height: 32px; font-size: 0.82rem; padding: 4px 8px; margin-bottom: 5px; max-width: 280px;">
                <div style="display: flex; gap: 5px;">
                  <button type="submit" name="status" value="approved" class="btn primary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer; border: 0;">Approve</button>
                  <button type="submit" name="status" value="rejected" class="btn secondary" style="min-height: 32px; padding: 0 10px; font-size: 0.85rem; font-weight: normal; cursor: pointer;">Reject</button>
                </div>
              </form>
            @else
              <span class="muted">Audited</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="muted" style="padding: 20px; text-align: center;">No documents pending review.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
