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
.auth-card .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.auth-card .form-row label { font-size: .85rem; font-weight: 400; margin: 0; display: flex; align-items: center; gap: 6px; cursor: pointer; }
.auth-card .form-row label input { width: auto; }
.auth-card .forgot { font-size: .85rem; color: #2563eb; }
.auth-card .forgot:hover { text-decoration: underline; }
.auth-btn { width: 100%; padding: 12px; background: #febd69; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; color: #131921; cursor: pointer; transition: background .25s ease; }
.auth-btn:hover { background: #f3a847; }
.auth-btn:disabled { opacity: .5; cursor: not-allowed; }
.auth-divider { display: flex; align-items: center; gap: 16px; margin: 24px 0; color: #64748b; font-size: .85rem; }
.auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
.social-btns { display: flex; gap: 10px; }
.social-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .85rem; font-weight: 500; cursor: pointer; background: #fff; transition: background .25s ease; }
.social-btn:hover { background: #f8fafc; }
.social-btn svg { width: 20px; height: 20px; }
.auth-switch { text-align: center; margin-top: 24px; font-size: .9rem; color: #64748b; }
.auth-switch a { color: #2563eb; font-weight: 600; }
.auth-switch a:hover { text-decoration: underline; }
.error-box { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 14px; border-radius: 8px; font-size: .85rem; margin-bottom: 18px; display: none; }
.error-box.show { display: block; }
.success-box { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 10px 14px; border-radius: 8px; font-size: .85rem; margin-bottom: 18px; display: none; }
.success-box.show { display: block; }
.toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #131921; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: .9rem; opacity: 0; transition: all .3s; z-index: 999; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <h1>Sign In</h1>
        <p class="sub">Welcome back to Adamawa Export Platform</p>

        @if($errors->any())
        <div class="error-box show">
            @foreach($errors->all() as $error) {{ $error }} @endforeach
        </div>
        @endif

        @if(session('success'))
        <div class="success-box show">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="form-row">
                <label><input type="checkbox" name="remember" checked> Remember me</label>
                <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
            </div>
            <button type="submit" class="auth-btn" id="loginBtn">Sign In</button>
        </form>

        <div class="auth-divider">or continue with</div>
        <div class="social-btns">
            <button class="social-btn" onclick="showToast('Google sign-in coming soon')">
                <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Google
            </button>
            <button class="social-btn" onclick="showToast('LinkedIn sign-in coming soon')">
                <svg viewBox="0 0 24 24" fill="#0A66C2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                LinkedIn
            </button>
        </div>

        <div class="auth-switch">Don't have an account? <a href="{{ route('register') }}">Create one</a></div>
    </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@section('scripts')
<script>
function showToast(msg) { var t = document.getElementById('toast'); if (t) { t.textContent = msg; t.classList.add('visible'); setTimeout(function() { t.classList.remove('visible'); }, 2500); } }
</script>
@endsection
