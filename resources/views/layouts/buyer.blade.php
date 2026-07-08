<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'All Products — Adamawa Export' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="{{ asset('assets/enterprise.css') }}">
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --accent: #f59e0b;
      --green: #16a34a;
      --bg: #ffffff;
      --bg-alt: #f8fafc;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --radius: 12px;
      --shadow: 0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.06);
      --shadow-lg: 0 10px 25px rgba(0,0,0,.1);
      --transition: .25s ease;
    }
    html { scroll-behavior: smooth; }
    body { font-family: system-ui,-apple-system,'Segoe UI',Roboto,sans-serif; color: var(--text); background: var(--bg); line-height: 1.6; }
    a { color: inherit; text-decoration: none; }
    button { cursor: pointer; font: inherit; border: none; background: none; }
    .container { max-width: 1286px; margin: 0 auto; padding: 0 24px; }

    /* ─── HEADER ─── */
    header { position: sticky; top: 0; z-index: 100; }
    .header-main { background: #131921; color: #fff; }
    .header-main .container { display: flex; align-items: center; gap: 16px; height: 60px; }
    .header-main .logo { font-size: 1.5rem; font-weight: 800; color: #fff; white-space: nowrap; flex-shrink: 0; text-decoration: none; }
    .header-main .logo span { color: #febd69; }
    .header-search {
      flex: 1; display: flex; max-width: 620px; margin: 0 auto; height: 40px; border: 2px solid transparent; border-radius: 6px; overflow: hidden; background: #fff; transition: border-color .2s, box-shadow .2s;
    }
    .header-search:focus-within { border-color: #febd69; box-shadow: 0 0 0 3px rgba(254,189,105,.25); }
    .header-search select { padding: 0 12px; border: none; background: #e8e8e8; font-size: .8rem; cursor: pointer; outline: none; color: #444; width: auto; max-width: 110px; border-right: 1px solid #d0d0d0; font-weight: 500; text-overflow: ellipsis; appearance: auto; }
    .header-search input { flex: 1; padding: 0 12px; border: none; font-size: .9rem; outline: none; min-width: 0; color: #000; }
    .header-search input::placeholder { color: #999; }
    .header-search button { width: 44px; background: #febd69; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: background .2s, transform .1s; flex-shrink: 0; color: #000; }
    .header-search button:hover { background: #f3a847; }
    .header-search button:active { transform: scale(.95); }
    .header-links { display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: auto; }
    .header-links a { padding: 4px 8px; border-radius: 3px; transition: background var(--transition); display: flex; flex-direction: column; text-decoration: none; }
    .header-links a:hover { background: rgba(255,255,255,.08); }
    .header-links a .top { font-size: .7rem; color: rgba(255,255,255,.7); line-height: 1.1; }
    .header-links a .bottom { font-size: .85rem; font-weight: 700; color: #fff; line-height: 1.2; }
    .header-cart { display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 3px; cursor: pointer; transition: background var(--transition); position: relative; }
    .header-cart:hover { background: rgba(255,255,255,.08); }
    .header-cart svg { width: 28px; height: 28px; }
    .header-cart .cart-text { display: flex; flex-direction: column; }
    .header-cart .cart-text .top { font-size: .7rem; color: rgba(255,255,255,.7); line-height: 1.1; }
    .header-cart .cart-text .bottom { font-size: .85rem; font-weight: 700; color: #fff; line-height: 1.2; }
    .header-cart .cart-count { position: absolute; top: 0; left: 22px; background: #f08804; color: #131921; font-size: .7rem; font-weight: 700; min-width: 18px; height: 16px; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 0 3px; opacity: 0; transform: scale(.5); transition: var(--transition); }
    .header-cart .cart-count.visible { opacity: 1; transform: scale(1); }
    .header-nav { background: #232f3e; height: 40px; }
    .header-nav .container { display: flex; align-items: center; height: 100%; overflow-x: auto; scrollbar-width: none; }
    .header-nav .container::-webkit-scrollbar { display: none; }
    .header-nav a, .header-nav span { font-size: .84rem; color: rgba(255,255,255,.85); padding: 6px 12px; border-radius: 3px; white-space: nowrap; cursor: pointer; text-decoration: none; }
    .header-nav a:hover { background: rgba(255,255,255,.1); color: #fff; }
    .header-nav .nav-departments { font-weight: 700; display: flex; align-items: center; gap: 6px; font-size: .84rem; color: #fff; padding: 5px 10px; border: 1px solid rgba(255,255,255,.3); border-radius: 3px; margin-right: 4px; cursor: default; }
    .auth-wrap { position: relative; display: inline-flex; align-items: center; }
    .auth-wrap a { padding: 4px 8px; border-radius: 3px; transition: background var(--transition); text-decoration: none; }
    .auth-wrap a:hover { background: rgba(255,255,255,.08); }
    .notif-icon { position: relative; cursor: pointer; padding: 4px; margin-right: 10px; display: flex; align-items: center; transition: opacity .2s; }
    .notif-icon:hover { opacity: .75; }
    .notif-icon .notif-count { position: absolute; top: 0; right: 0; background: #ef4444; color: #fff; font-size: .6rem; font-weight: 700; min-width: 16px; height: 14px; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 0 3px; line-height: 1; }
    .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #febd69; color: #131921; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; cursor: pointer; flex-shrink: 0; transition: transform .15s; }
    .user-avatar:hover { transform: scale(1.08); }
    .user-dropdown { position: absolute; top: 42px; right: 0; background: #fff; border-radius: 8px; box-shadow: var(--shadow-lg); min-width: 210px; display: none; overflow: hidden; z-index: 200; }
    .user-dropdown.open { display: block; }
    .user-dropdown .header { padding: 14px 16px; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .user-dropdown .header strong { font-size: .9rem; display: block; color: var(--text); }
    .user-dropdown .header span { font-size: .78rem; color: var(--text-muted); }
    .user-dropdown a { display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: .85rem; color: var(--text) !important; transition: background var(--transition); text-decoration: none; }
    .user-dropdown a:hover { background: var(--bg-alt); }
    .user-dropdown a .badge { margin-left: auto; font-size: .65rem; padding: 2px 8px; border-radius: 50px; font-weight: 600; background: #fef3c7; color: #92400e; }
    .user-dropdown a .badge.verified { background: #d1fae5; color: #065f46; }
    .user-dropdown hr { border: none; border-top: 1px solid var(--border); margin: 0; }
    @media (max-width: 720px) {
      .header-links a:not(.hide-mobile) { display: none; }
      .header-search select { width: 40px; }
      .auth-wrap.hide-mobile { display: none !important; }
    }
    .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: var(--text); color: #fff; padding: 14px 28px; border-radius: 50px; font-weight: 500; font-size: .92rem; opacity: 0; transition: all .35s cubic-bezier(.22,1,.36,1); z-index: 300; pointer-events: none; }
    .toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
  </style>
</head>
<body>

  <header>
    <div class="header-main">
      <div class="container">
        <div style="display:flex; align-items:center; gap:12px;">
          <a href="{{ url('/') }}" class="logo">Adamawa<span>Export</span></a>
          <div style="display:inline-flex; align-items:center; gap:8px;">
            <div class="country-selector hide-mobile" style="position:relative;">
              <button type="button" onclick="toggleCountryDropdown(event)" style="display:inline-flex; align-items:center; gap:6px; background:#232f3e; border:1px solid #3a4b5f; border-radius:6px; padding:4px 10px; color:#fff; font-size:0.75rem; font-weight:600; cursor:pointer; transition:0.2s;">
                <span id="headerCountryFlag" style="font-size:0.95rem;">@if(session('user_country', 'NG')=='NG') 🇳🇬 @elseif(session('user_country')=='US') 🇺🇸 @elseif(session('user_country')=='GB') 🇬🇧 @elseif(session('user_country')=='DE') 🇩🇪 @elseif(session('user_country')=='FR') 🇫🇷 @elseif(session('user_country')=='CN') 🇨🇳 @elseif(session('user_country')=='AE') 🇦🇪 @elseif(session('user_country')=='ZA') 🇿🇦 @else 🌍 @endif</span>
                <span id="headerCountryCode">{{ session('user_country', 'NG') }}</span>
                <span id="headerCountryCurr" style="color:#febd69;">({{ session('user_currency', 'NGN') }})</span>
                <span style="font-size:0.55rem; opacity:0.7;">▼</span>
              </button>
              <div id="countryDropdownMenu" style="position:absolute; top:100%; left:0; margin-top:6px; background:#131921; border:1px solid #3a4b5f; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.5); width:190px; z-index:99999; display:none; padding:6px 0; text-align:left;">
                <div style="padding:4px 12px; font-size:0.65rem; color:#94a3b8; text-transform:uppercase; font-weight:700; border-bottom:1px solid #232f3e; margin-bottom:4px;">Select Destination</div>
                <a href="#" onclick="selectCountryOverride(event, 'NG', 'Nigeria', 'NGN')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇳🇬 Nigeria <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">NGN</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'US', 'United States', 'USD')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇺🇸 United States <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">USD</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'GB', 'United Kingdom', 'EUR')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇬🇧 United Kingdom <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">EUR</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'DE', 'Germany', 'EUR')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇩🇪 Germany <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">EUR</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'FR', 'France', 'EUR')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇫🇷 France <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">EUR</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'CN', 'China', 'USD')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇨🇳 China <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">USD</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'AE', 'United Arab Emirates', 'USD')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇦🇪 UAE <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">USD</span></a>
                <a href="#" onclick="selectCountryOverride(event, 'ZA', 'South Africa', 'USD')" style="display:flex; align-items:center; gap:8px; padding:6px 12px; color:#fff; font-size:0.8rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.background='#232f3e'" onmouseout="this.style.background='transparent'">🇿🇦 South Africa <span style="margin-left:auto; color:#febd69; font-size:0.7rem;">USD</span></a>
              </div>
            </div>
            <div class="currency-switcher hide-mobile" style="display:inline-flex; align-items:center; background:#232f3e; border-radius:6px; padding:2px; border:1px solid #3a4b5f; font-size:0.75rem;">
              <button type="button" onclick="setCurrency('NGN')" id="curr-NGN" class="curr-btn active" style="padding:4px 8px; border:none; background:#febd69; color:#131921; font-weight:800; border-radius:4px; cursor:pointer;">₦ NGN</button>
              <button type="button" onclick="setCurrency('USD')" id="curr-USD" class="curr-btn" style="padding:4px 8px; border:none; background:transparent; color:#fff; font-weight:600; cursor:pointer;">$ USD</button>
              <button type="button" onclick="setCurrency('EUR')" id="curr-EUR" class="curr-btn" style="padding:4px 8px; border:none; background:transparent; color:#fff; font-weight:600; cursor:pointer;">€ EUR</button>
            </div>
          </div>
        </div>

        <div style="position:relative; flex:1; max-width:600px;">
          <form method="GET" action="{{ route('buyer.products.index') }}" class="header-search" style="margin:0; width:100%;">
            <select name="category[]" onchange="if(this.value===''){window.location.href='{{ route('buyer.products.index') }}'}else{this.form.submit()}">
              <option value="">All</option>
              @foreach($sharedCategories as $cat)
                <option value="{{ $cat->slug }}" {{ in_array($cat->slug, (array)request('category', [])) ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
            <input type="text" name="search" id="liveSearchInput" value="{{ request('search') }}" placeholder="Search products..." autocomplete="off" oninput="handleLiveSearch(this.value)">
            <button type="submit">🔍</button>
          </form>
          <div id="liveSearchDropdown" style="position:absolute; top:100%; left:0; right:0; background:#fff; color:#0f172a; border-radius:10px; box-shadow:0 15px 35px rgba(0,0,0,0.25); z-index:9999; display:none; max-height:380px; overflow-y:auto; border:1px solid #cbd5e1; margin-top:6px;"></div>
        </div>

        <div class="header-links">
          <div class="auth-wrap hide-mobile" id="authWrap">
            @auth
            <div id="authUser" style="position:relative; display:inline-flex; align-items:center;">
              <div class="notif-icon" id="notifIcon" onclick="window.location.href='{{ route('buyer.orders.index') }}'">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="notif-count" id="notifCount">0</span>
              </div>
              <div class="user-avatar" id="userAvatar" onclick="toggleDropdown(event)">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
              <div class="user-dropdown" id="userDropdown">
                <div class="header" id="dropdownHeader"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div>
                @php
                    $dashboardRoute = 'admin.dashboard';
                    $profileRoute = 'admin.profile';
                    if (Auth::user()->hasRole('seller')) {
                        $dashboardRoute = 'buyer.dashboard';
                        $profileRoute = 'buyer.profile.show';
                    } elseif (Auth::user()->hasRole('buyer')) {
                        $dashboardRoute = 'buyer.dashboard';
                        $profileRoute = 'buyer.profile.show';
                    } elseif (Auth::user()->hasRole('logistics')) {
                        $dashboardRoute = 'logistics.dashboard';
                    }
                @endphp
                <a href="{{ route($dashboardRoute) }}">📊 Dashboard</a>
                @if(Auth::user()->hasRole('seller'))
                  <a href="{{ route('seller.dashboard') }}">🏪 My Store</a>
                @endif
                <a href="{{ route($profileRoute) }}">👤 My Profile</a>
                @if(Auth::user()->hasRole('buyer'))
                  <a href="{{ route('buyer.orders.index') }}">📦 My Orders</a>
                  <a href="{{ route('buyer.cart.index') }}">🛒 My Cart</a>
                @elseif(Auth::user()->hasRole('seller'))
                  <a href="{{ route('seller.orders.index') }}">📦 Orders</a>
                @endif
                <hr>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                  @csrf
                  <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">🚪 Sign Out</a>
                </form>
              </div>
            </div>
            @else
            <a href="{{ route('login') }}" id="authSignIn">
              <span class="top">Hello</span>
              <span class="bottom">Sign In</span>
            </a>
            @endauth
          </div>
          <a href="{{ route('buyer.orders.index') }}" class="hide-mobile">
            <span class="top">Returns</span>
            <span class="bottom">& Orders</span>
          </a>
          <div class="header-cart" onclick="toggleCart()">
            <svg viewBox="0 0 24 24" fill="#fff"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
            <div class="cart-text">
              <span class="bottom">Cart</span>
            </div>
            <span class="cart-count" id="cartCount">0</span>
          </div>
        </div>
      </div>
    </div>
    <div class="header-nav">
      <div class="container">
        <span class="nav-departments"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><circle cx="4" cy="4" r="2"/><circle cx="12" cy="4" r="2"/><circle cx="20" cy="4" r="2"/><circle cx="4" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="20" cy="12" r="2"/><circle cx="4" cy="20" r="2"/><circle cx="12" cy="20" r="2"/><circle cx="20" cy="20" r="2"/></svg> Browse</span>
        <a href="{{ url('/') }}#local">Local Goods</a>
        <a href="{{ route('buyer.products.index') }}" style="color:#fff; font-weight:700;">Products</a>
        <a href="{{ url('/') }}#import">Overseas Import</a>
        <a href="{{ url('/') }}#how">How It Works</a>
        <a href="{{ url('/') }}#testimonials">Testimonials</a>
      </div>
    </div>
  </header>

  <main>
    @if(View::hasSection('full_width_content'))
      @yield('full_width_content')
    @else
      <div class="container" style="padding-top: 24px; padding-bottom: 40px;">
        @if(session('success'))
            <div style="background-color: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #664d03; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                ⚠️ {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background-color: #f8d7da; border: 1px solid #f5c2c7; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                ❌ {{ session('error') }}
            </div>
        @endif
        @yield('content')
      </div>
    @endif
  </main>

  <!-- Footer -->
  <footer class="bg-[#232f3e] text-white text-xs mt-10">
      <div class="border-b border-white/10">
          <div class="max-w-[1500px] mx-auto px-4 py-8">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                  <div>
                      <h4 class="font-bold text-sm mb-3">Get to Know Us</h4>
                      <ul class="space-y-1.5 text-white/60">
                          <li><a href="#" class="hover:underline">About ATEX</a></li>
                          <li><a href="#" class="hover:underline">Careers</a></li>
                          <li><a href="#" class="hover:underline">Press Center</a></li>
                      </ul>
                  </div>
                  <div>
                      <h4 class="font-bold text-sm mb-3">Make Money with Us</h4>
                      <ul class="space-y-1.5 text-white/60">
                          <li><a href="{{ route('seller.onboarding') }}" class="hover:underline">Sell on ATEX</a></li>
                          <li><a href="#" class="hover:underline">Become a Logistics Partner</a></li>
                          <li><a href="#" class="hover:underline">Advertise Your Products</a></li>
                      </ul>
                  </div>
                  <div>
                      <h4 class="font-bold text-sm mb-3">Let Us Help You</h4>
                      <ul class="space-y-1.5 text-white/60">
                          <li><a href="#" class="hover:underline">Your Account</a></li>
                          <li><a href="#" class="hover:underline">Your Orders</a></li>
                          <li><a href="#" class="hover:underline">Shipping Rates & Policies</a></li>
                      </ul>
                  </div>
                  <div>
                      <h4 class="font-bold text-sm mb-3">Connect</h4>
                      <ul class="space-y-1.5 text-white/60">
                          <li><a href="#" class="hover:underline">Facebook</a></li>
                          <li><a href="#" class="hover:underline">Twitter</a></li>
                          <li><a href="#" class="hover:underline">Instagram</a></li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
      <div class="py-4 text-center text-white/40 text-[11px]">
          &copy; {{ date('Y') }} Adamawa Export Market (ATEX). All rights reserved.
      </div>
  </footer>

  <!-- ═══ CART OVERLAY & SIDEBAR ═══ -->
  <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()" style="position:fixed;inset:0;background:rgba(0,0,0,.4);opacity:0;pointer-events:none;transition:opacity var(--transition);z-index:200"></div>
  <aside class="cart-sidebar" id="cartSidebar" style="position:fixed;top:0;right:0;width:420px;max-width:90vw;height:100vh;background:var(--bg);z-index:201;transform:translateX(100%);transition:transform .35s cubic-bezier(.22,1,.36,1);display:flex;flex-direction:column">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:24px;border-bottom:1px solid var(--border)">
      <h2 style="font-size:1.25rem; font-weight:700; margin:0;">Cart</h2>
      <button onclick="toggleCart()" style="width:36px;height:36px;border-radius:50%;background:var(--bg-alt);font-size:1.2rem;border:none;cursor:pointer">✕</button>
    </div>
    <div class="cart-items" id="cartItems" style="flex:1;overflow-y:auto;padding:16px 24px">
      <div class="cart-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--text-muted);text-align:center;padding:40px">
        <p>Your cart is empty</p>
      </div>
    </div>
    <div style="padding:24px;border-top:1px solid var(--border)">
      <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700;margin-bottom:16px"><span>Total</span><span id="cartTotal">₦0.00</span></div>
      <a href="{{ route('buyer.cart.index') }}" style="display:block;text-align:center;padding:8px;font-size:.85rem;color:var(--primary);text-decoration:none;">View Full Cart →</a>
      <button onclick="handleCheckout()" style="width:100%;padding:16px;border-radius:50px;background:var(--text);color:#fff;font-weight:600;font-size:1rem;border:none;cursor:pointer;transition:background var(--transition)" onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--text)'">Checkout</button>
    </div>
  </aside>

  <div class="toast" id="toast"></div>

  <script>
    let cart = JSON.parse(localStorage.getItem('gt_cart') || localStorage.getItem('atex_cart') || '[]');

    function toggleCart() {
      const overlay = document.getElementById('cartOverlay');
      const sidebar = document.getElementById('cartSidebar');
      if (!overlay || !sidebar) return;
      const isOpen = overlay.style.opacity === '1';
      overlay.style.opacity = isOpen ? '0' : '1';
      overlay.style.pointerEvents = isOpen ? 'none' : 'auto';
      sidebar.style.transform = isOpen ? 'translateX(100%)' : 'translateX(0px)';
    }

    function addToCartItem(item) {
      const existing = cart.find(c => c.id == item.id);
      if (existing) { existing.qty++; } else { cart.push({ ...item, qty: 1 }); }
      updateCartUI();
      showToast(`${item.name} added to cart`);
    }

    function removeFromCart(id) { cart = cart.filter(c => c.id != id); updateCartUI(); }

    function changeQty(id, delta) {
      const item = cart.find(c => c.id == id);
      if (!item) return;
      item.qty += delta;
      if (item.qty <= 0) { removeFromCart(id); return; }
      updateCartUI();
    }

    function parsePriceVal(val) {
      if (!val) return 0;
      if (typeof val === 'number') return val;
      const cleaned = String(val).replace(/[^0-9.]/g, '');
      return parseFloat(cleaned) || 0;
    }

    function updateCartUI() {
      const count = cart.reduce((s, c) => s + c.qty, 0);
      localStorage.setItem('gt_cart', JSON.stringify(cart));
      localStorage.setItem('atex_cart', JSON.stringify(cart));
      const ce = document.getElementById('cartCount');
      if (ce) { ce.textContent = count; ce.classList.toggle('visible', count > 0); }
      const label = document.getElementById('cartLabel');
      if (label) label.textContent = count > 0 ? count : '';
      const container = document.getElementById('cartItems');
      const totalEl = document.getElementById('cartTotal');
      if (!container) return;
      if (cart.length === 0) {
        container.innerHTML = '<div class="cart-empty" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:var(--text-muted);text-align:center;padding:40px"><p>Your cart is empty</p></div>';
        if (totalEl) totalEl.textContent = '₦0.00';
        return;
      }
      container.innerHTML = cart.map(c => `
        <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--border)">
          <div style="width:48px;height:48px;border-radius:8px;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0">${c.emoji || '📦'}</div>
          <div style="flex:1">
            <div style="font-size:.88rem;font-weight:600">${c.name}</div>
            <div style="font-size:.8rem;color:var(--text-muted)">${isNaN(parseFloat(c.price)) && String(c.price).match(/[^0-9.,]/) ? c.price : '₦' + parsePriceVal(c.price).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
            <div style="display:flex;align-items:center;gap:6px;margin-top:4px">
              <button onclick="changeQty('${c.id}',-1)" style="width:24px;height:24px;border-radius:50%;background:var(--bg-alt);font-weight:600;font-size:.85rem;border:none;cursor:pointer">−</button>
              <span style="font-weight:600;font-size:.85rem;min-width:16px;text-align:center">${c.qty}</span>
              <button onclick="changeQty('${c.id}',1)" style="width:24px;height:24px;border-radius:50%;background:var(--bg-alt);font-weight:600;font-size:.85rem;border:none;cursor:pointer">+</button>
              <button onclick="removeFromCart('${c.id}')" style="font-size:.78rem;color:var(--text-muted);padding:2px 6px;border:none;cursor:pointer;background:none">✕</button>
            </div>
          </div>
        </div>
      `).join('');
      const total = cart.reduce((s, c) => s + parsePriceVal(c.price) * c.qty, 0);
      if (totalEl) totalEl.textContent = `₦${total.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    function handleCheckout() {
      if (cart.length === 0) { showToast('Cart is empty'); return; }
      const total = cart.reduce((sum, c) => sum + parsePriceVal(c.price) * c.qty, 0);
      showToast('Proceeding to checkout — ₦' + total.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
      setTimeout(() => window.location.href = '{{ route("buyer.cart.index") }}', 1000);
    }

    function showToast(msg) {
      const el = document.getElementById('toast');
      if (!el) return;
      el.textContent = msg;
      el.classList.add('visible');
      clearTimeout(el._timeout);
      el._timeout = setTimeout(() => el.classList.remove('visible'), 3000);
    }

    function toggleDropdown(e) { e.stopPropagation(); document.getElementById('userDropdown')?.classList.toggle('open'); }
    document.addEventListener('click', function() { const dd = document.getElementById('userDropdown'); if (dd && dd.classList.contains('open')) dd.classList.remove('open'); });

    updateCartUI();
    if (typeof lucide !== 'undefined') lucide.createIcons();
  </script>
  <script>var isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};</script>
  @include('layouts.ux')
  @stack('scripts')
</body>
</html>
