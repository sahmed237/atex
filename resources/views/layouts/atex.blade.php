<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Adamawa Ecommerce platform Enterprise')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Original Atex Styling -->
    <link rel="stylesheet" href="{{ asset('assets/enterprise.css') }}">

    <style>
        :root {
            --primary-color: #2563eb;
            --sidebar-bg: #0f172a;
            --sidebar-accent: #1e293b;
            --sidebar-scrollbar: rgba(255, 255, 255, 0.1);
        }
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        .fancy-scroll::-webkit-scrollbar { width: 5px; }
        .fancy-scroll::-webkit-scrollbar-track { background: transparent; }
        .fancy-scroll::-webkit-scrollbar-thumb { background: var(--sidebar-scrollbar); border-radius: 10px; }
        .fancy-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .sidebar-active {
            background-color: var(--primary-color) !important;
            color: white !important;
        }

        /* Prevent legacy enterprise layout wrapper conflicts */
        .shell { display: block; min-height: auto; }
        .content { margin: 0; padding: 0; background: transparent; }
        .topline { display: none; }
    </style>
</head>
<body class="h-full overflow-hidden" x-data="{ sidebarOpen: true }">
  @php
    $user = Auth::user();
    $role = 'guest';
    $items = [];
    $active = $active ?? '';

    if ($user) {
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $role = $user->hasRole('super-admin') ? 'super-admin' : 'admin';
            $items = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
            ];
            if ($user->hasRole('admin')) {
                $items[] = ['route' => 'kyc.onboarding', 'label' => 'KYC Profile', 'icon' => 'file-check-2'];
            }
            $items = array_merge($items, [
                ['route' => 'admin.atex.users.index', 'label' => 'Users', 'icon' => 'users'],
                ['route' => 'admin.kyc.index', 'label' => 'KYC Verification', 'icon' => 'file-check-2'],
                ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => 'box'],
                ['route' => 'admin.documents.index', 'label' => 'Documents', 'icon' => 'file-text'],
                ['route' => 'admin.quotes.index', 'label' => 'RFQs', 'icon' => 'message-square'],
                ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'shopping-cart'],
                ['route' => 'admin.inventory.index', 'label' => 'Inventory', 'icon' => 'warehouse'],
                ['route' => 'admin.fulfillment.index', 'label' => 'Fulfillment', 'icon' => 'package'],
                ['route' => 'admin.settlements.index', 'label' => 'Settlements', 'icon' => 'wallet'],
                ['route' => 'admin.audit.index', 'label' => 'Audit Logs', 'icon' => 'clipboard-list'],
            ]);
        } elseif ($user->hasRole('seller')) {
            $role = 'seller';
            $items = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                ['route' => 'admin.profile.show', 'label' => 'My Profile', 'icon' => 'user'],
                ['route' => 'kyc.onboarding', 'label' => 'KYC Profile', 'icon' => 'file-check-2'],
                ['route' => 'admin.products.index', 'label' => 'My Products', 'icon' => 'box'],
                ['route' => 'admin.documents.index', 'label' => 'My Documents', 'icon' => 'file-text'],
                ['route' => 'admin.quotes.index', 'label' => 'My RFQs', 'icon' => 'message-square'],
                ['route' => 'admin.orders.index', 'label' => 'My Orders', 'icon' => 'shopping-cart'],
            ];
        } elseif ($user->hasRole('logistics')) {
            $role = 'logistics';
            $items = [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                ['route' => 'admin.profile.show', 'label' => 'My Profile', 'icon' => 'user'],
                ['route' => 'kyc.onboarding', 'label' => 'KYC Profile', 'icon' => 'file-check-2'],
                ['route' => 'admin.orders.index', 'label' => 'Assigned Shipments', 'icon' => 'truck'],
            ];
        }
    }
  @endphp

    <div class="flex h-full">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 text-white transition-transform duration-300 transform lg:relative lg:translate-x-0"
            style="background-color: var(--sidebar-bg)"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b" style="border-color: rgba(255,255,255,0.1)">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="h-8 w-auto">
                    <button @click="sidebarOpen = false" class="lg:hidden text-white/50 hover:text-white">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto fancy-scroll">
                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">{{ str_replace('_', ' ', $role) }} Menu</div>
                    @foreach ($items as $item)
                        @php
                            $isActive = request()->routeIs($item['route']) || ($item['route'] === 'admin.dashboard' && request()->routeIs('admin.dashboard'));
                        @endphp
                        <a href="{{ route($item['route']) }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ $isActive ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 mr-3"></i>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <!-- User Profile -->
                <a href="{{ route('admin.profile') }}" class="block p-4 m-4 rounded-2xl transition-all hover:bg-white/10 group" style="background-color: rgba(255,255,255,0.05)">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-inner transition-transform group-hover:scale-110" style="background-color: var(--primary-color)">
                            {{ substr($user->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="text-sm font-medium text-white truncate">{{ $user->name ?? 'Admin User' }}</p>
                            <p class="text-xs text-slate-400 truncate group-hover:text-primary-400 transition-colors">Settings & Profile</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto text-white/20 group-hover:text-white transition-all transform group-hover:translate-x-1"></i>
                    </div>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="h-16 glass z-40 px-6 flex items-center justify-between border-b border-gray-200">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 lg:hidden mr-4">
                        <i data-lucide="menu" class="w-6 h-6 text-slate-600"></i>
                    </button>
                    <h1 class="text-xl font-bold text-slate-800">@yield('header_title', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if(Auth::check() && !Auth::user()->hasRole('seller') && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super-admin'))
                        <a href="{{ route('seller.onboarding') }}" class="text-sm font-medium text-green-600 hover:text-green-800 transition-colors hidden sm:block">Become a Seller</a>
                        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                    @endif
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors hidden sm:block">Back to Landing</a>
                    <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center text-sm font-medium text-gray-600 hover:text-red-600 transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5 mr-2"></i>
                            <span class="hidden sm:inline">Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
