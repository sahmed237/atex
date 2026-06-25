@extends('layouts.landing')

@section('styles')
<style>
.auth-page { display: flex; min-height: calc(100vh - 100px); align-items: center; justify-content: center; padding: 40px 24px; }
.auth-card { background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,.1); width: 100%; max-width: 440px; padding: 40px; }
.auth-card h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 4px; color: #0f172a; }
.auth-card .sub { color: #64748b; font-size: .9rem; margin-bottom: 28px; }
.auth-card .form-group { margin-bottom: 18px; }
.auth-card label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 6px; color: #0f172a; }
.auth-card input { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .9rem; outline: none; transition: border-color .25s ease; background: #fff; }
.auth-card input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
.auth-btn { width: 100%; padding: 12px; background: #febd69; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; color: #131921; cursor: pointer; transition: background .25s ease; }
.auth-btn:hover { background: #f3a847; }
.auth-btn:disabled { opacity: .5; cursor: not-allowed; }
.auth-switch { text-align: center; margin-top: 24px; font-size: .9rem; color: #64748b; }
.auth-switch a { color: #2563eb; font-weight: 600; }
.auth-switch a:hover { text-decoration: underline; }
.error-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: .85rem; margin-bottom: 18px; display: none; }
.error-box.show { display: block; }
.success-box { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px; border-radius: 8px; font-size: .9rem; margin-bottom: 18px; display: none; }
.success-box.show { display: block; }
.toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #131921; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: .9rem; opacity: 0; transition: all .3s; z-index: 999; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Reset Password</h1>
        <p class="sub">Enter your email and we'll send you a reset link</p>

        @if($errors->any())
        <div class="error-box show">
            @foreach($errors->all() as $error) {{ $error }} @endforeach
        </div>
        @endif

        @if(session('success'))
        <div class="success-box show">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
            </div>
            <button type="submit" class="auth-btn" id="fpBtn">Send Reset Link</button>
        </form>

        <div class="auth-switch"><a href="{{ route('login') }}">← Back to Sign In</a></div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
function showToast(msg) { var t = document.getElementById('toast'); if (t) { t.textContent = msg; t.classList.add('visible'); setTimeout(function() { t.classList.remove('visible'); }, 2500); } }
</script>
@endsection
