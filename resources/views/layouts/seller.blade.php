<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }} - Revenue Collection System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&family=Montserrat:wght@300;400;600;700&family=Poppins:wght@300;400;600;700&family=Outfit:wght@300;400;600;700&family=Lato:wght@300;400;700&family=Nunito:wght@300;400;600;700&family=Raleway:wght@300;400;600;700&family=Ubuntu:wght@300;400;500;700&family=Quicksand:wght@300;400;600;700&family=Fira+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
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
            --primary-color: {{ $system_settings['theme_primary_color'] ?? '#2563eb' }};
            --sidebar-bg: {{ $system_settings['theme_sidebar_bg'] ?? '#0f172a' }};
            --sidebar-accent: {{ $system_settings['theme_sidebar_accent'] ?? '#1e293b' }};
            --sidebar-scrollbar: {{ $system_settings['theme_sidebar_scrollbar_color'] ?? 'rgba(255, 255, 255, 0.1)' }};
        }
        body { font-family: '{{ $system_settings['theme_font_family'] ?? 'Inter' }}', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar */
        .fancy-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .fancy-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .fancy-scroll::-webkit-scrollbar-thumb {
            background: var(--sidebar-scrollbar);
            border-radius: 10px;
        }
        .fancy-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

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

        /* Buyer Layout styles */
        .amazon-nav { background: #131921; }
        .amazon-subnav { background: #232f3e; }
        .amazon-gold { color: #febd69; }
        .amazon-btn {
            background: #ffd814;
            border-color: #fcd200;
            color: #0f1111;
        }
        .amazon-btn:hover { background: #f7ca00; }
        .amazon-btn-secondary {
            background: #ffa41c;
            border-color: #ff8f00;
            color: #0f1111;
        }
        .amazon-btn-secondary:hover { background: #fa8900; }

    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '{{ $system_settings['theme_primary_color'] ?? '#2563eb' }}10',
                            100: '{{ $system_settings['theme_primary_color'] ?? '#2563eb' }}20',
                            500: '{{ $system_settings['theme_primary_color'] ?? '#2563eb' }}',
                            600: '{{ $system_settings['theme_primary_color'] ?? '#2563eb' }}',
                        },
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full overflow-hidden" x-data="{ sidebarOpen: true }">
    <div class="flex h-full">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 text-white transition-transform duration-300 transform lg:relative lg:translate-x-0"
            style="background-color: {{ $system_settings['theme_sidebar_bg'] ?? '#0f172a' }}"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-between h-16 px-6 border-b" style="border-color: rgba(255,255,255,0.1)">
                    @if(!empty($system_settings['platform_logo']))
                        <img src="{{ $system_settings['platform_logo'] }}" alt="Logo" class="h-8 w-auto">
                    @else
                        <span class="text-xl font-bold tracking-wider text-white">{{ $system_settings['platform_name'] ?? 'URCS' }}</span>
                    @endif
                    <button @click="sidebarOpen = false" class="lg:hidden text-white/50 hover:text-white">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto fancy-scroll">
                    <a href="{{ route('seller.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.dashboard') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    
                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">My Export Business</div>
                    
                    <a href="{{ route('seller.catalog.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.catalog.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="box" class="w-5 h-5 mr-3"></i>
                        Catalog
                    </a>
                    <a href="{{ route('seller.inventory.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.inventory.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="warehouse" class="w-5 h-5 mr-3"></i>
                        Inventory
                    </a>
                    <a href="{{ route('seller.orders.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.orders.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5 mr-3"></i>
                        My Orders
                    </a>
                    <a href="{{ route('seller.documents.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.documents.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                        Compliance Docs
                    </a>
                    <a href="{{ route('seller.profile.show') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.profile.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Company Profile
                    </a>
                </nav>

                @php
                    $bottomProfileRoute = 'seller.profile.show';
                @endphp
                <a href="{{ route($bottomProfileRoute) }}" class="block p-4 m-4 rounded-2xl transition-all hover:bg-white/10 group" style="background-color: rgba(255,255,255,0.05)">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-inner transition-transform group-hover:scale-110" style="background-color: var(--primary-color)">
                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                            <p class="text-xs text-slate-400 truncate group-hover:text-primary-400 transition-colors">Settings & Profile</p>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 ml-auto text-white/20 group-hover:text-white transition-all transform group-hover:translate-x-1"></i>
                    </div>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header - Amazon style -->
            <header class="amazon-nav text-white h-[60px] z-40 px-6 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4 h-full">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded hover:outline hover:outline-1 hover:outline-white/30 lg:hidden text-white">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    
                    <!-- Search Bar -->
                    <div class="hidden lg:flex flex-1 group w-[400px]">
                        <div class="flex h-[40px] items-center w-full">
                            <input type="text" placeholder="Search for orders, products..." 
                                   class="flex-1 h-full px-3 text-sm text-black outline-none rounded-l-lg border-none focus:ring-2 focus:ring-[#febd69]"
                                   autocomplete="off">
                            <button class="h-full rounded-r-lg amazon-gold bg-[#febd69] hover:bg-[#f3a847] text-black px-4 flex items-center justify-center transition-colors">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-white/90">
                    <a href="{{ route('buyer.products.index') }}" class="flex flex-col leading-tight px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded text-xs items-center justify-center h-[40px]">
                        <span class="text-sm font-bold amazon-gold">Switch to Buyer</span>
                    </a>
                    
                    <button class="relative p-2 hover:outline hover:outline-1 hover:outline-white/30 rounded h-[40px] flex items-center justify-center text-white">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border border-[#131921]"></span>
                    </button>
                    
                    <div class="h-8 w-px bg-white/20 mx-2"></div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="h-[40px] flex items-center">
                        @csrf
                        <button type="submit" class="flex items-center px-2 py-1 text-sm font-medium hover:outline hover:outline-1 hover:outline-white/30 rounded text-white h-full">
                            <i data-lucide="log-out" class="w-4 h-4 mr-1.5"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Notifications Toast -->
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            init() {
                @if(session('success'))
                    this.notify('{{ session('success') }}', 'success');
                @endif
                @if(session('error'))
                    this.notify('{{ session('error') }}', 'error');
                @endif
                @if(session('info'))
                    this.notify('{{ session('info') }}', 'info');
                @endif
                @if(session('warning'))
                    this.notify('{{ session('warning') }}', 'warning');
                @endif
            },
            notify(msg, type) {
                this.message = msg;
                this.type = type;
                this.show = true;
                setTimeout(() => { this.show = false }, 5000);
            }
         }" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-10 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-10 opacity-0"
         class="fixed bottom-8 right-8 z-[100] max-w-sm w-full"
         style="display: none;">
        
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                 :class="{
                    'bg-emerald-50 text-emerald-600': type === 'success',
                    'bg-red-50 text-red-600': type === 'error',
                    'bg-indigo-50 text-indigo-600': type === 'info',
                    'bg-amber-50 text-amber-600': type === 'warning'
                 }">
                <i data-lucide="check-circle" class="w-6 h-6" x-show="type === 'success'"></i>
                <i data-lucide="alert-circle" class="w-6 h-6" x-show="type === 'error'"></i>
                <i data-lucide="info" class="w-6 h-6" x-show="type === 'info'"></i>
                <i data-lucide="alert-triangle" class="w-6 h-6" x-show="type === 'warning'"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-slate-800" x-text="message"></p>
            </div>
            <button @click="show = false" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
