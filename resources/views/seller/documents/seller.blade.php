@extends('layouts.seller')

@section('title', 'My Documents | Adamawa Ecommerce platform')
@section('header_title', 'My Documents')

@section('content')
<section class="grid two" style="align-items: start;">
  <!-- Document Upload Form -->
  <div class="panel">
    <div class="panel-head">
      <h2>Upload Compliance Document</h2>
      <span class="status pending">Upload</span>
    </div>
    <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="form-grid" style="margin-top: 20px; display: grid; gap: 15px;">
      @csrf
      <label>Document Title
        <input name="title" required placeholder="e.g. Phytosanitary Certificate - Lot A">
      </label>
      
      <label>Document Type
        <select name="document_type" required>
          <option value="business_registration">Business Registration</option>
          <option value="tax_identification">Tax Identification</option>
          <option value="phytosanitary">Phytosanitary Certificate</option>
          <option value="quality_report">Quality Inspection Report</option>
          <option value="general">General License</option>
        </select>
      </label>

      <label>Expiry Date
        <input type="date" name="expiry_date">
      </label>

      <label>Select Document File (PDF, Images, Word - Max 5MB)
        <input type="file" name="document_file" required>
      </label>

      <button type="submit" class="btn primary full" style="margin-top: 10px;">Upload Document</button>
    </form>
  </div>

  <!-- Documents List -->
  <div class="panel">
    <div class="panel-head">
      <h2>Document Wallet</h2>
      <span class="status active">{{ count($documents) }} Documents</span>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
      <thead>
        <tr style="text-align: left; border-bottom: 1px solid var(--line);">
          <th style="padding: 10px 5px;">Document Details</th>
          <th style="padding: 10px 5px;">Type</th>
          <th style="padding: 10px 5px;">Status</th>
          <th style="padding: 10px 5px;">Expiry</th>
        </tr>
      </thead>
      <tbody>
        @forelse($documents as $doc)
          <tr style="border-bottom: 1px solid var(--soft);">
            <td style="padding: 12px 5px;">
              <strong>{{ $doc->title }}</strong><br>
              @if($doc->path)
                <a href="{{ asset($doc->path) }}" target="_blank" class="muted" style="text-decoration: underline; font-size: 0.85rem;">View File</a>
              @endif
              @if($doc->review_comment)
                <div style="font-size: 0.82rem; margin-top: 5px; color: #b83a35; padding: 4px; background: #fdf3f2; border-left: 2px solid #b83a35;">
                  <strong>Comment:</strong> {{ $doc->review_comment }}
                </div>
              @endif
            </td>
            <td style="padding: 12px 5px;">{{ str_replace('_', ' ', $doc->document_type) }}</td>
            <td style="padding: 12px 5px;">
              <span class="status {{ $doc->status }}">{{ $doc->status }}</span>
            </td>
            <td style="padding: 12px 5px;">{{ $doc->expiry_date ? $doc->expiry_date->format('M d, Y') : 'Permanent' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="muted" style="padding: 20px; text-align: center;">No documents found in wallet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection
