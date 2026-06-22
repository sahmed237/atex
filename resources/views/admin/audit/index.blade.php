@extends('layouts.admin')

@section('title', 'System Audit Trail | Adamawa Ecommerce platform')
@section('header_title', 'System Audit Trail')

@section('content')
<section class="panel">
  <div class="panel-head">
    <h2>Audit Logs (YTD)</h2>
    <span class="status active">Secured</span>
  </div>
  <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.9rem;">
    <thead>
      <tr style="text-align: left; border-bottom: 1px solid var(--line);">
        <th style="padding: 10px 5px; width: 180px;">Timestamp</th>
        <th style="padding: 10px 5px;">Actor</th>
        <th style="padding: 10px 5px;">Action</th>
        <th style="padding: 10px 5px;">Module / ID</th>
        <th style="padding: 10px 5px;">Old Values</th>
        <th style="padding: 10px 5px;">New Values</th>
        <th style="padding: 10px 5px;">IP Address</th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $log)
        <tr style="border-bottom: 1px solid var(--soft);">
          <td style="padding: 12px 5px;">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
          <td style="padding: 12px 5px;">
            <strong>{{ $log->actor->name ?? 'System' }}</strong><br>
            <small class="muted">{{ $log->actor->email ?? '' }}</small>
          </td>
          <td style="padding: 12px 5px;">
            <code style="background: var(--soft); padding: 2px 4px; border-radius: 4px; color: var(--deep);">{{ $log->action }}</code>
          </td>
          <td style="padding: 12px 5px;">
            {{ $log->auditable_type }} (ID: {{ $log->auditable_id }})
          </td>
          <td style="padding: 12px 5px; font-family: monospace; font-size: 0.8rem; word-break: break-all;">
            {{ is_array($log->old_values) ? json_encode($log->old_values) : $log->old_values }}
          </td>
          <td style="padding: 12px 5px; font-family: monospace; font-size: 0.8rem; word-break: break-all;">
            {{ is_array($log->new_values) ? json_encode($log->new_values) : $log->new_values }}
          </td>
          <td style="padding: 12px 5px;">{{ $log->ip_address }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="muted" style="padding: 20px; text-align: center;">No audit logs recorded.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</section>
@endsection
