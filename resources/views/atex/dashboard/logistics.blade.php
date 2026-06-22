@extends('layouts.admin')

@section('title', 'Logistics Dashboard | Adamawa Ecommerce platform')
@section('header_title', 'Logistics Dashboard')

@section('content')
<section class="grid stats">
  <article class="card">
    <span class="muted">Assigned Shipments</span>
    <strong>{{ $metrics['assigned_shipments'] }}</strong>
    <small>Assigned cargo trips</small>
  </article>
  <article class="card">
    <span class="muted">In Transit</span>
    <strong>{{ $metrics['in_transit_shipments'] }}</strong>
    <small>Moving shipments</small>
  </article>
  <article class="card">
    <span class="muted">Delivered</span>
    <strong>{{ $metrics['delivered_shipments'] }}</strong>
    <small>Successfully delivered</small>
  </article>
  <article class="card">
    <span class="muted">Unassigned Pool</span>
    <strong>{{ $metrics['pending_assignment'] }}</strong>
    <small>Trips seeking driver</small>
  </article>
</section>

<section class="grid two" style="margin-top: 30px;">
  <div class="panel">
    <div class="panel-head">
      <h2>Logistics Partner Profile</h2>
      <span class="status {{ $profile->verification_status }}">{{ $profile->verification_status }}</span>
    </div>
    <p style="margin-top: 15px;"><strong>{{ $profile->company_name }}</strong></p>
    <p class="muted" style="margin-top: 5px;">Regions Covered: {{ $profile->coverage_regions ?: 'Not specified' }}</p>
    <div class="actions" style="margin-top: 20px;">
      <a class="btn primary" href="{{ route('admin.profile.show') }}">Update Profile</a>
      <a class="btn secondary" href="{{ route('admin.orders.index') }}">Manage Shipments</a>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Transport Capability</h2>
      <span class="status active">Active</span>
    </div>
    <p style="margin-top: 15px;"><strong>Modes:</strong> {{ $profile->transport_modes ?: 'Not specified' }}</p>
    <p style="margin-top: 5px;"><strong>Base:</strong> {{ $profile->base_location ?: 'Not specified' }}</p>
    <p style="margin-top: 5px;"><strong>Fleet Capacity:</strong> {{ $profile->fleet_capacity ?: 'Not specified' }}</p>
  </div>
</section>
@endsection
