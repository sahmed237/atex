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
                    @php
                        $dashboardRoute = 'admin.dashboard';
                        if(auth()->user()->hasRole('seller')) $dashboardRoute = 'seller.dashboard';
                        elseif(auth()->user()->hasRole('buyer')) $dashboardRoute = 'buyer.dashboard';
                        elseif(auth()->user()->hasRole('logistics')) $dashboardRoute = 'logistics.dashboard';
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs($dashboardRoute) ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>
                    
                    @hasanyrole('super-admin|admin')
                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">Export Market</div>
                    
                    <a href="{{ route('admin.atex.users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.atex.users.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                        AEM Users
                    </a>
                    <div x-data="{ open: {{ request()->routeIs('admin.kyc.*') ? 'true' : 'false' }} }">
                        <button type="button" @click="open = !open" class="w-full flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.kyc.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="file-check-2" class="w-5 h-5 mr-3"></i>
                            <span class="flex-1 text-left">KYC Verification</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition class="mt-1 ml-4 pl-3 space-y-1 border-l border-white/10">
                            <a href="{{ route('admin.kyc.index', ['type' => 'seller']) }}" class="flex items-center px-4 py-2 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.kyc.*') && request('type') === 'seller' ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                                <i data-lucide="store" class="w-4 h-4 mr-3"></i>
                                Seller
                            </a>
                            <a href="{{ route('admin.kyc.index', ['type' => 'export']) }}" class="flex items-center px-4 py-2 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.kyc.*') && request('type') === 'export' ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                                <i data-lucide="globe" class="w-4 h-4 mr-3"></i>
                                Exporter
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('admin.sellers.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.sellers.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
                        Sellers
                    </a>
                    <a href="{{ route('admin.buyers.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.buyers.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="shopping-bag" class="w-5 h-5 mr-3"></i>
                        Buyers
                    </a>
                    <a href="{{ route('admin.logistics.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.logistics.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="truck" class="w-5 h-5 mr-3"></i>
                        Logistics Partners
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.products.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="box" class="w-5 h-5 mr-3"></i>
                        Products
                    </a>
                    <a href="{{ route('admin.documents.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.documents.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                        Documents
                    </a>
                    <a href="{{ route('admin.quotes.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.quotes.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="message-square" class="w-5 h-5 mr-3"></i>
                        RFQs
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.orders.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5 mr-3"></i>
                        Orders
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.inventory.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="warehouse" class="w-5 h-5 mr-3"></i>
                        Inventory
                    </a>
                    <a href="{{ route('admin.fulfillment.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.fulfillment.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="package" class="w-5 h-5 mr-3"></i>
                        Fulfillment
                    </a>
                    <a href="{{ route('admin.settlements.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.settlements.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="wallet" class="w-5 h-5 mr-3"></i>
                        Settlements
                    </a>
                    <a href="{{ route('admin.payouts.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.payouts.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="banknote" class="w-5 h-5 mr-3"></i>
                        My Payouts
                    </a>
                    <a href="{{ route('admin.audit.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.audit.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5 mr-3"></i>
                        Audit Logs
                    </a>
                    <a href="{{ route('admin.profile.show') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.profile.show') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        My Profile
                    </a>

                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">Administration</div>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.users.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                        User Management
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.roles.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="shield-check" class="w-5 h-5 mr-3"></i>
                        Roles & Permissions
                    </a>
                    <a href="{{ route('admin.legal-documents.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.legal-documents.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="scale" class="w-5 h-5 mr-3"></i>
                        Legal Documents
                    </a>
                    @if(auth()->user()->hasRole('super-admin'))
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('admin.settings.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                            <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
                            System Settings
                        </a>
                    @endif
                    
                    @if(auth()->user()->hasAnyPermission(['view business category', 'manage business category', 'view product categories', 'manage product categories', 'view packaging', 'manage packaging', 'view units', 'manage units']))
                        <div x-data="{ open: {{ request()->routeIs('admin.business-categories.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.product-packaging.*') || request()->routeIs('admin.units.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                                <div class="flex items-center">
                                    <i data-lucide="list" class="w-5 h-5 mr-3"></i>
                                    System Options
                                </div>
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="{'rotate-180': open}"></i>
                            </button>
                            
                            <div x-show="open" x-collapse class="pl-12 pr-4 py-2 space-y-1">
                                @can('view business category')
                                <a href="{{ route('admin.business-categories.index') }}" class="block px-3 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('admin.business-categories.*') ? 'text-white bg-white/10 font-medium' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                                    Business Categories
                                </a>
                                @endcan
                                @can('view product categories')
                                <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('admin.categories.*') ? 'text-white bg-white/10 font-medium' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                                    Product Categories
                                </a>
                                @endcan
                                @can('view packaging')
                                <a href="{{ route('admin.product-packaging.index') }}" class="block px-3 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('admin.product-packaging.*') ? 'text-white bg-white/10 font-medium' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                                    Product Packaging
                                </a>
                                @endcan
                                @can('view units')
                                <a href="{{ route('admin.units.index') }}" class="block px-3 py-2 text-sm transition-colors rounded-lg {{ request()->routeIs('admin.units.*') ? 'text-white bg-white/10 font-medium' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                                    Units of Measurement
                                </a>
                                @endcan
                            </div>
                        </div>
                    @endif
                    @endhasanyrole

                    @hasrole('seller')
                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">My Export Business</div>
                    
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="box" class="w-5 h-5 mr-3"></i>
                        My Products
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="warehouse" class="w-5 h-5 mr-3"></i>
                        Inventory
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="message-square" class="w-5 h-5 mr-3"></i>
                        Received RFQs
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="shopping-cart" class="w-5 h-5 mr-3"></i>
                        My Orders
                    </a>
                    <a href="{{ route('seller.compliance.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('seller.compliance.index') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                        Compliance Docs
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="banknote" class="w-5 h-5 mr-3"></i>
                        Payments & Payouts
                    </a>
                    <a href="{{ route('admin.profile.show') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Company Profile
                    </a>
                    @endhasrole

                    @hasrole('buyer')
                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">Buying & Sourcing</div>
                    
                    <a href="{{ route('buyer.products.index') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="search" class="w-5 h-5 mr-3"></i>
                        Browse Products
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="message-square" class="w-5 h-5 mr-3"></i>
                        My RFQs
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="shopping-cart" class="w-5 h-5 mr-3"></i>
                        Purchase Orders
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="package" class="w-5 h-5 mr-3"></i>
                        Track Shipments
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="credit-card" class="w-5 h-5 mr-3"></i>
                        Payment History
                    </a>
                    <a href="{{ route('buyer.profile.show') }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl {{ request()->routeIs('buyer.profile.*') ? 'sidebar-active' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Profile
                    </a>
                    @endhasrole

                    @hasrole('logistics')
                    <div class="pt-4 pb-2 text-xs font-semibold tracking-wider uppercase text-white/30">Logistics Operations</div>
                    
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="truck" class="w-5 h-5 mr-3"></i>
                        Active Shipments
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="map-pin" class="w-5 h-5 mr-3"></i>
                        Update Tracking
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="file-check" class="w-5 h-5 mr-3"></i>
                        Shipping Documents
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="banknote" class="w-5 h-5 mr-3"></i>
                        Earnings & Payouts
                    </a>
                    <a href="#" class="flex items-center px-4 py-3 text-sm font-medium transition-colors rounded-xl text-white/60 hover:bg-white/10 hover:text-white">
                        <i data-lucide="user" class="w-5 h-5 mr-3"></i>
                        Company Profile
                    </a>
                    @endhasrole
                </nav>

                @php
                    $bottomProfileRoute = 'admin.profile.show';
                    if(auth()->user()->hasRole('buyer')) $bottomProfileRoute = 'buyer.profile.show';
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
            <!-- Header -->
            <header class="h-16 glass z-40 px-6 flex items-center justify-between border-b border-gray-200">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 lg:hidden">
                    <i data-lucide="menu" class="w-6 h-6 text-slate-600"></i>
                </button>
                
                <div class="hidden lg:flex items-center bg-gray-100 rounded-full px-4 py-2 w-96">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 mr-2"></i>
                    <input type="text" placeholder="Search for shops, payments..." class="bg-transparent border-none focus:ring-0 text-sm w-full">
                </div>

                <div class="flex items-center space-x-4">
                    <button class="relative p-2 text-gray-400 hover:text-primary-600 transition-colors">
                        <i data-lucide="bell" class="w-6 h-6"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                    <div class="h-8 w-px bg-gray-200"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center text-sm font-medium text-gray-600 hover:text-red-600 transition-colors">
                            <i data-lucide="log-out" class="w-5 h-5 mr-2"></i>
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
