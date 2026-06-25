@extends('layouts.landing')

@section('styles')
<style>
.auth-page { display: flex; min-height: calc(100vh - 100px); align-items: center; justify-content: center; padding: 40px 24px; }
.auth-card { background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,.1); width: 100%; max-width: 440px; padding: 40px; text-align: center; }
.auth-card h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 4px; color: #0f172a; }
.auth-card .sub { color: #64748b; font-size: .9rem; margin-bottom: 28px; line-height: 1.6; }
.auth-card .sub strong { color: #0f172a; }
.auth-btn { width: 100%; padding: 12px; background: #febd69; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; color: #131921; cursor: pointer; transition: background .25s ease; display: flex; align-items: center; justify-content: center; gap: 8px; }
.auth-btn:hover { background: #f3a847; }
.auth-btn:disabled { opacity: .5; cursor: not-allowed; }
.auth-btn-secondary { width: 100%; padding: 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .9rem; font-weight: 600; color: #64748b; cursor: pointer; transition: background .25s ease; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 12px; }
.auth-btn-secondary:hover { background: #f8fafc; }
.auth-switch { text-align: center; margin-top: 24px; font-size: .9rem; color: #64748b; }
.auth-switch a { color: #2563eb; font-weight: 600; }
.auth-switch a:hover { text-decoration: underline; }
.error-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: .85rem; margin-bottom: 18px; display: none; }
.error-box.show { display: block; }
.success-box { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 10px 14px; border-radius: 8px; font-size: .85rem; margin-bottom: 18px; display: none; }
.success-box.show { display: block; }
.toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #131921; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: .9rem; opacity: 0; transition: all .3s; z-index: 999; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
.auth-icon { width: 64px; height: 64px; margin: 0 auto 20px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.auth-icon svg { width: 32px; height: 32px; color: #f59e0b; }
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>

        <h1>Verification Required</h1>
        <p class="sub">Your email address <strong>{{ $email }}</strong> has not been verified yet. Please check your inbox for the activation link.</p>

        @if(session('success'))
        <div class="success-box show">{{ session('success') }}</div>
        @endif

        @if(session('error'))
        <div class="error-box show">{{ session('error') }}</div>
        @endif

        <div>
            @if(($system_settings['user_can_request_new_email_verification'] ?? '1') == '1')
                <form action="{{ route('verification.resend') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="auth-btn">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4Z"/></svg>
                        Resend Verification Link
                    </button>
                </form>
            @else
                <div style="padding: 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; margin-bottom: 12px;">
                    <p style="font-size: .8rem; color: #64748b; line-height: 1.5;">
                        If you did not receive the verification email, please contact your system administrator to resend the activation link or manually verify your account.
                    </p>
                </div>
            @endif

            <a href="{{ route('login') }}" class="auth-btn-secondary">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                Back to Login
            </a>

            @if(app()->environment('local'))
                <form action="{{ route('verification.bypass') }}" method="POST" style="margin-top: 12px;">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="auth-btn" style="background: #16a34a;">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        Auto-Approve Verification (Local Dev)
                    </button>
                </form>
            @endif
        </div>

        <div class="auth-switch">
            Having trouble? <a href="#">Contact support</a>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
function showToast(msg) { var t = document.getElementById('toast'); if (t) { t.textContent = msg; t.classList.add('visible'); setTimeout(function() { t.classList.remove('visible'); }, 2500); } }
</script>
@endsection
