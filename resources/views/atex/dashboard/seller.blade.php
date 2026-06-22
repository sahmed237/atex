@extends('layouts.admin')

@section('title', 'Seller Dashboard | Adamawa Ecommerce platform')
@section('header_title', 'Seller Dashboard')

@section('content')
<section class="grid stats">
  <article class="card">
    <span class="muted">My Products</span>
    <strong>{{ $metrics['products'] }}</strong>
    <small>Total listings</small>
  </article>
  <article class="card">
    <span class="muted">Pending Products</span>
    <strong>{{ $metrics['pending_products'] }}</strong>
    <small>Awaiting review</small>
  </article>
  <article class="card">
    <span class="muted">Inventory Lots</span>
    <strong>{{ $metrics['inventory_items'] }}</strong>
    <small>Stored in warehouse</small>
  </article>
  <article class="card">
    <span class="muted">My Documents</span>
    <strong>{{ $metrics['documents'] }}</strong>
    <small>Uploaded compliance</small>
  </article>
  <article class="card">
    <span class="muted">Pending Documents</span>
    <strong>{{ $metrics['pending_documents'] }}</strong>
    <small>Under verification</small>
  </article>
  <article class="card">
    <span class="muted">RFQs Received</span>
    <strong>{{ $metrics['quotes'] }}</strong>
    <small>Quotes requested</small>
  </article>
  <article class="card">
    <span class="muted">My Orders</span>
    <strong>{{ $metrics['orders'] }}</strong>
    <small>Buyer transactions</small>
  </article>
  <article class="card">
    <span class="muted">Pending Payout</span>
    <strong>USD {{ number_format((float) $metrics['pending_payout'], 2) }}</strong>
    <small>Awaiting settlement</small>
  </article>
  <article class="card">
    <span class="muted">Credited Payout</span>
    <strong>USD {{ number_format((float) $metrics['credited_payout'], 2) }}</strong>
    <small>Transferred to bank</small>
  </article>
  <article class="card">
    <span class="muted">Tracked Value</span>
    <strong>USD {{ number_format((float) $metrics['export_value'], 2) }}</strong>
    <small>Total order trade value</small>
  </article>
  <article class="card">
    <span class="muted">Readiness</span>
    <strong>{{ $metrics['readiness'] }}%</strong>
    <small>Compliance score</small>
  </article>
</section>

<section class="grid two" style="margin-top: 30px;">
  <div class="panel">
    <div class="panel-head">
      <h2>Seller Profile</h2>
      <span class="status {{ $profile->verification_status }}">{{ str_replace('_', ' ', $profile->verification_status) }}</span>
    </div>
    <p style="margin-top: 15px;"><strong>{{ $profile->business_name }}</strong></p>
    <p class="muted" style="margin-top: 5px;">{{ $profile->address ?: 'No business address provided.' }}</p>
    <div class="actions" style="margin-top: 20px;">
      <a class="btn primary" href="{{ route('admin.profile.show') }}">Update Profile</a>
      <a class="btn secondary" href="{{ route('admin.products.index') }}">Manage Products</a>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Seller Program</h2>
      <span class="status {{ $profile->seller_program_status }}">{{ str_replace('_', ' ', $profile->seller_program_status) }}</span>
    </div>
    <p style="margin-top: 15px;"><strong>Brand Name:</strong> {{ $profile->seller_brand_name ?: $profile->business_name }}</p>
    <p style="margin-top: 5px;"><strong>Fulfillment Model:</strong> {{ str_replace('_', ' ', $profile->fulfillment_model) }}</p>
    <p class="muted" style="margin-top: 10px;">Your products and compliance documents enter the admin verification queue automatically. Product visibility on the public market shelf requires approved profile status.</p>
  </div>
</section>
@endsection
