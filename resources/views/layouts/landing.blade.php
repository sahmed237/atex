<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    @yield('styles')
  </head>
  <body>
    <header class="topbar">
      <a class="brand" href="{{ route('home') }}" aria-label="Adamawa Ecommerce platform home">
        <span class="brand-mark"><img src="{{ asset('assets/logo.png') }}" alt="Adamawa Ecommerce platform logo" /></span>
        <span>
          <strong>Adamawa Ecommerce platform</strong>
          <small>Verified non-oil trade portal</small>
        </span>
      </a>

      <nav class="main-nav" aria-label="Primary navigation">
        &nbsp;
      </nav>

      <div class="top-actions">
        @auth
          @if(!Auth::user()->hasRole('seller') && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin'))
            <a class="btn ghost text-green-600 font-bold" href="{{ route('seller.onboarding') }}">Become a Seller</a>
          @endif
          @php
            $dashboardRoute = 'admin.dashboard';
            if(Auth::user()->hasRole('seller')) $dashboardRoute = 'seller.dashboard';
            elseif(Auth::user()->hasRole('buyer')) $dashboardRoute = 'buyer.dashboard';
            elseif(Auth::user()->hasRole('logistics')) $dashboardRoute = 'logistics.dashboard';
          @endphp
          <a class="btn ghost" href="{{ route($dashboardRoute) }}">Dashboard</a>
        @else
          <a class="btn ghost" href="{{ route('register') }}">Register</a>
          <a class="btn ghost" href="{{ route('login') }}">Login</a>
        @endauth
        <a class="btn primary" href="{{ route('home') }}#marketplace">Browse</a>
      </div>
    </header>

    <main>
      @yield('content')
    </main>

    @yield('scripts')
  </body>
</html>
