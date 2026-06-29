<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Adamawa Ecommerce platform') }}</title>
    <link rel="preconnect" href="https://images.unsplash.com" />
    <link rel="stylesheet" href="{{ asset('assets/landing.css') }}" />
    @isset($marketplaceProducts)
    <script>
      window.marketplaceProducts = {!! json_encode($marketplaceProducts) !!};
    </script>
    @endisset
    <script src="{{ asset('assets/landing.js') }}" defer></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" async onload="window.lucide && window.lucide.createIcons()"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        .amazon-nav { background: #131921; font-family: 'Inter', sans-serif; }
        .amazon-subnav { background: #232f3e; font-family: 'Inter', sans-serif; }
        .amazon-gold { color: #febd69; }
        .custom-scrollbar::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.25); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.4); }
        .custom-scrollbar { scrollbar-width: thin; scrollbar-color: rgba(255, 255, 255, 0.25) transparent; }
        [x-cloak] { display: none !important; }

        /* ─── ENTERPRISE HEADER STYLES ─── */
        header { position: sticky; top: 0; z-index: 100; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; }
        .header-main { background: #131921; color: #fff; }
        .header-main .container { display: flex; align-items: center; gap: 16px; height: 60px; max-width: 1286px; margin: 0 auto; padding: 0 24px; box-sizing: border-box; }
        .header-main .logo { font-size: 1.5rem; font-weight: 800; color: #fff; white-space: nowrap; flex-shrink: 0; text-decoration: none; }
        .header-main .logo span { color: #febd69; }
        .header-search { flex: 1; display: flex; max-width: 620px; margin: 0 auto; height: 40px; border: 2px solid transparent; border-radius: 6px; overflow: hidden; background: #fff; transition: border-color .2s, box-shadow .2s; }
        .header-search:focus-within { border-color: #febd69; box-shadow: 0 0 0 3px rgba(254,189,105,.25); }
        .header-search select { padding: 0 12px; border: none; background: #e8e8e8; font-size: .8rem; cursor: pointer; outline: none; color: #444; width: auto; max-width: 110px; border-right: 1px solid #d0d0d0; font-weight: 500; text-overflow: ellipsis; appearance: auto; }
        .header-search input { flex: 1; padding: 0 12px; border: none; font-size: .9rem; outline: none; min-width: 0; color: #000; margin: 0; }
        .header-search input::placeholder { color: #999; }
        .header-search button { width: 44px; background: #febd69; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: background .2s, transform .1s; flex-shrink: 0; color: #000; padding: 0; }
        .header-search button:hover { background: #f3a847; }
        .header-search button:active { transform: scale(.95); }
        .header-links { display: flex; align-items: center; gap: 6px; flex-shrink: 0; margin-left: auto; }
        .header-links a { padding: 4px 8px; border-radius: 3px; transition: background .25s ease; display: flex; flex-direction: column; text-decoration: none; color: #fff; }
        .header-links a:hover { background: rgba(255,255,255,.08); }
        .header-links a .top { font-size: .7rem; color: rgba(255,255,255,.7); line-height: 1.1; }
        .header-links a .bottom { font-size: .85rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .header-cart { display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 3px; cursor: pointer; transition: background .25s ease; position: relative; }
        .header-cart:hover { background: rgba(255,255,255,.08); }
        .header-cart svg { width: 28px; height: 28px; }
        .header-cart .cart-text { display: flex; flex-direction: column; }
        .header-cart .cart-text .bottom { font-size: .85rem; font-weight: 700; color: #fff; line-height: 1.2; }
        .header-cart .cart-count { position: absolute; top: 0; left: 22px; background: #f08804; color: #131921; font-size: .7rem; font-weight: 700; min-width: 18px; height: 16px; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 0 3px; transition: .25s ease; }
        .header-nav { background: #232f3e; height: 40px; color: #fff; }
        .header-nav .container { display: flex; align-items: center; height: 100%; overflow-x: auto; scrollbar-width: none; max-width: 1286px; margin: 0 auto; padding: 0 24px; box-sizing: border-box; }
        .header-nav .container::-webkit-scrollbar { display: none; }
        .header-nav a, .header-nav span { font-size: .84rem; color: rgba(255,255,255,.85); padding: 6px 12px; border-radius: 3px; white-space: nowrap; cursor: pointer; text-decoration: none; }
        .header-nav a:hover { background: rgba(255,255,255,.1); color: #fff; }
        .header-nav .nav-departments { font-weight: 700; display: flex; align-items: center; gap: 6px; font-size: .84rem; color: #fff; padding: 5px 10px; border: 1px solid rgba(255,255,255,.3); border-radius: 3px; margin-right: 4px; cursor: default; }
        .auth-wrap { position: relative; display: inline-flex; align-items: center; }
        .auth-wrap a { padding: 4px 8px; border-radius: 3px; transition: background .25s ease; text-decoration: none; display: flex; flex-direction: column; }
        .auth-wrap a:hover { background: rgba(255,255,255,.08); }
        .notif-icon { position: relative; cursor: pointer; padding: 4px; margin-right: 10px; display: flex; align-items: center; transition: opacity .2s; }
        .notif-icon:hover { opacity: .75; }
        .notif-icon .notif-count { position: absolute; top: 0; right: 0; background: #ef4444; color: #fff; font-size: .6rem; font-weight: 700; min-width: 16px; height: 14px; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 0 3px; line-height: 1; }
        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #febd69; color: #131921; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; cursor: pointer; flex-shrink: 0; transition: transform .15s; }
        .user-avatar:hover { transform: scale(1.08); }
        .user-dropdown { position: absolute; top: 42px; right: 0; background: #fff; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,.1); min-width: 210px; display: none; overflow: hidden; z-index: 200; }
        .user-dropdown.open { display: block; }
        .user-dropdown .header { padding: 14px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .user-dropdown .header strong { font-size: .9rem; display: block; color: #0f172a; }
        .user-dropdown .header span { font-size: .78rem; color: #64748b; }
        .user-dropdown a { display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: .85rem; color: #0f172a !important; transition: background .25s ease; text-decoration: none; }
        .user-dropdown a:hover { background: #f8fafc; }
        .user-dropdown a .badge { margin-left: auto; font-size: .65rem; padding: 2px 8px; border-radius: 50px; font-weight: 600; background: #fef3c7; color: #92400e; }
        .user-dropdown a .badge.verified { background: #d1fae5; color: #065f46; }
        .user-dropdown hr { border: none; border-top: 1px solid #e2e8f0; margin: 0; }
        @media (max-width: 720px) {
          .header-links a:not(.hide-mobile) { display: none; }
          .header-search select { width: 40px; }
          .auth-wrap.hide-mobile { display: none !important; }
          .hide-mobile { display: none !important; }
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: { preflight: false },
            theme: {
                extend: {
                    colors: {
                        amazon: { dark: '#131921', navy: '#232f3e', gold: '#febd69' }
                    }
                }
            }
        }
    </script>
    @yield('styles')
  </head>
  <body>
    <header>
    <div class="header-main">
      <div class="container">
        <div style="display:flex; align-items:center; gap:12px;">
          <a href="{{ url('/') }}" class="logo">Adamawa<span>Export</span></a>
          <div class="currency-switcher hide-mobile" style="display:inline-flex; align-items:center; background:#232f3e; border-radius:6px; padding:2px; border:1px solid #3a4b5f; font-size:0.75rem;">
            <button type="button" onclick="setCurrency('NGN')" id="curr-NGN" class="curr-btn active" style="padding:4px 8px; border:none; background:#febd69; color:#131921; font-weight:800; border-radius:4px; cursor:pointer;">₦ NGN</button>
            <button type="button" onclick="setCurrency('USD')" id="curr-USD" class="curr-btn" style="padding:4px 8px; border:none; background:transparent; color:#fff; font-weight:600; cursor:pointer;">$ USD</button>
            <button type="button" onclick="setCurrency('EUR')" id="curr-EUR" class="curr-btn" style="padding:4px 8px; border:none; background:transparent; color:#fff; font-weight:600; cursor:pointer;">€ EUR</button>
          </div>
        </div>

        <div style="position:relative; flex:1; max-width:600px;">
          <form method="GET" action="{{ route('buyer.products.index') }}" class="header-search" style="margin:0; width:100%;">
            <select name="category[]" onchange="this.form.submit()">
              <option value="">All</option>
              @foreach($sharedCategories as $cat)
                <option value="{{ $cat->slug }}" {{ in_array($cat->slug, request('category', [])) ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                <a href="{{ route('buyer.profile.show') }}">🔐 KYC Verification <span class="badge verified">Verified</span></a>
                <a href="{{ route('buyer.orders.index') }}">📦 My Orders</a>
                <a href="{{ route('buyer.cart.index') }}">🛒 My Cart</a>
                <hr>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                  @csrf
                  <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">🚪 Sign Out</a>
                </form>
              </div>
            </div>
            @else
            <a href="http://127.0.0.1:8000/login" id="authSignIn">
              <span class="top">Hello</span>
              <span class="bottom">Sign In</span>
            </a>
            @endauth
          </div>
          <a href="http://127.0.0.1:8000/orders" class="hide-mobile">
            <span class="top">Returns</span>
            <span class="bottom">&amp; Orders</span>
          </a>
          <div class="header-cart" onclick="toggleCart()">
            <svg viewBox="0 0 24 24" fill="#fff"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49A1.003 1.003 0 0 0 20 4H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"></path></svg>
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
        <a href="http://127.0.0.1:8000#local">Local Goods</a>
        <a href="http://127.0.0.1:8000/products" style="color:#fff; font-weight:700;">Products</a>
        <a href="http://127.0.0.1:8000#import">Overseas Import</a>
        <a href="http://127.0.0.1:8000#how">How It Works</a>
        <a href="http://127.0.0.1:8000#testimonials">Testimonials</a>
      </div>
    </div>
  </header>

    <main>
      @yield('content')
    </main>

    @include('layouts.ux')
    @yield('scripts')

    <script>var isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};</script>
  </body>
</html>
