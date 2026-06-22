@extends('layouts.admin')

@section('title', 'Operations Dashboard | Adamawa Ecommerce platform')
@section('header_title', 'Operations Dashboard')

@section('content')
<section class="grid stats">
  <article class="card">
    <span class="muted">Users</span>
    <strong>{{ $metrics['users'] }}</strong>
    <small>Registered logins</small>
  </article>
  <article class="card">
    <span class="muted">Pending Users</span>
    <strong>{{ $metrics['pending_users'] }}</strong>
    <small>Awaiting approval</small>
  </article>
  <article class="card">
    <span class="muted">Sellers</span>
    <strong>{{ $metrics['sellers'] }}</strong>
    <small>Seller profiles</small>
  </article>
  <article class="card">
    <span class="muted">Buyers</span>
    <strong>{{ $metrics['buyers'] }}</strong>
    <small>Global buyer accounts</small>
  </article>
  <article class="card">
    <span class="muted">Pending KYC</span>
    <strong>{{ $metrics['pending_kyc'] }}</strong>
    <small>Profiles in verification</small>
  </article>
  <article class="card">
    <span class="muted">Products</span>
    <strong>{{ $metrics['products'] }}</strong>
    <small>Total catalog listings</small>
  </article>
  <article class="card">
    <span class="muted">Open RFQs</span>
    <strong>{{ $metrics['open_quotes'] }}</strong>
    <small>Active negotiations</small>
  </article>
  <article class="card">
    <span class="muted">Pending Docs</span>
    <strong>{{ $metrics['pending_documents'] }}</strong>
    <small>In review queues</small>
  </article>
  <article class="card">
    <span class="muted">Orders</span>
    <strong>{{ $metrics['orders'] }}</strong>
    <small>Total trade orders</small>
  </article>
  <article class="card">
    <span class="muted">Inventory Lots</span>
    <strong>{{ $metrics['inventory_items'] }}</strong>
    <small>AfriBridge received stock</small>
  </article>
  <article class="card">
    <span class="muted">Pending Settlements</span>
    <strong>{{ $metrics['pending_settlements'] }}</strong>
    <small>Ledger release queues</small>
  </article>
  <article class="card">
    <span class="muted">Tracked Value</span>
    <strong>USD {{ number_format((float) $metrics['export_value'], 2) }}</strong>
    <small>Total trade value</small>
  </article>
</section>

<section class="grid two" style="margin-top: 30px;">
  <div class="panel">
    <div class="panel-head">
      <h2>Enterprise Build Status</h2>
      <span class="status active">Active</span>
    </div>
    <p style="margin-top: 15px;">This Laravel-backed portal integrates role-aware validation, product reviews, document compliance wallets, order dispatch flows, and payout release actions.</p>
  </div>
  <div class="panel">
    <div class="panel-head">
      <h2>Operations Roadmap</h2>
      <span class="status pending">Pending</span>
    </div>
    <p style="margin-top: 15px;">Monitor registration workflow, verify seller documents, catalog marketplace products, and manage logistics and payout releases.</p>
  </div>
</section>
@endsection
