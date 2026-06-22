<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Adamawa Ecommerce platform' }} - Adamawa Ecommerce platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('assets/enterprise.css') }}">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
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
        .product-card {
            background: #fff;
            border: 1px solid #e7e7e7;
            transition: all 0.15s ease;
        }
        .product-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .product-card img { mix-blend-mode: multiply; }
        .rating-star { color: #de7921; }
        .price-symbol { font-size: 0.7rem; vertical-align: super; }
        .link-blue { color: #007185; }
        .link-blue:hover { color: #c7511f; text-decoration: underline; }
        .section-title { font-size: 1.25rem; font-weight: 700; color: #0f1111; }
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amazon: { dark: '#131921', navy: '#232f3e', gold: '#febd69' }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-[#eaeded]">
    <div class="flex flex-col min-h-full">
        <!-- Top Nav - Amazon style -->
        <header class="amazon-nav text-white sticky top-0 z-50">
            <div class="max-w-[1500px] mx-auto px-4">
                <div class="flex items-center h-[60px] gap-4">
                    <!-- Logo -->
                    <a href="{{ route('buyer.products.index') }}" class="flex items-center gap-1 shrink-0 hover:outline hover:outline-1 hover:outline-white/30 px-2 py-1 rounded">
                        <span class="text-2xl font-bold tracking-tight">ATEX</span>
                        <span class="text-xs amazon-gold font-medium leading-none mt-2">.ng</span>
                    </a>

                    <!-- Search Bar -->
                    <div class="flex-1 group">
                        <div class="flex h-[40px] items-center gap-1.5">
                            <select class="w-[50px] bg-white text-black text-xs px-2 rounded-lg border border-gray-300 outline-none cursor-pointer h-full">
                                <option>All</option>
                            </select>
                            <input type="text" id="search-input" placeholder="Search ATEX"
                                   class="flex-1 h-full px-3 text-sm text-black outline-none rounded-lg border border-gray-300 focus:border-[#febd69] transition-colors"
                                   autocomplete="off">
                            <button class="h-full rounded-lg amazon-gold bg-[#febd69] hover:bg-[#f3a847] text-black px-4 flex items-center justify-center transition-colors" onclick="performSearch()">
                                <i data-lucide="search" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Right Links -->
                    <div class="flex items-center gap-2 text-white/90">
                        <a href="{{ route('buyer.profile.show') }}" class="flex flex-col leading-tight px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded text-xs">
                            <span class="text-[11px] text-white/60">Hello, {{ Str::words(auth()->user()->name ?? 'Guest', 1, '') }}</span>
                            <span class="text-sm font-bold">Account</span>
                        </a>
                        <a href="#" class="flex flex-col leading-tight px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded text-xs">
                            <span class="text-[11px] text-white/60">Returns</span>
                            <span class="text-sm font-bold">& Orders</span>
                        </a>
                        <a href="#" class="flex items-center gap-1 px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded">
                            <i data-lucide="shopping-cart" class="w-[22px] h-[22px]"></i>
                            <span class="text-sm font-bold">Cart</span>
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sub Nav -->
        <nav class="amazon-subnav text-white text-sm sticky top-[60px] z-40">
            <div class="max-w-[1500px] mx-auto px-4 flex items-center h-[40px] gap-5 overflow-x-auto">
                <a href="{{ route('buyer.products.index') }}" class="flex items-center gap-1.5 whitespace-nowrap font-medium px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded">
                    <i data-lucide="home" class="w-[18px] h-[18px]"></i>
                </a>
                <a href="{{ route('buyer.products.index') }}" class="whitespace-nowrap font-medium px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded">All Products</a>
                <a href="{{ route('buyer.dashboard') }}" class="whitespace-nowrap font-medium px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded">My Dashboard</a>
                <a href="{{ route('buyer.profile.show') }}" class="whitespace-nowrap font-medium px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded">My Profile</a>
                @hasrole('seller')
                <a href="{{ route('seller.dashboard') }}" class="whitespace-nowrap font-medium px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded text-[#febd69]">My Store</a>
                @else
                <a href="{{ route('seller.onboarding') }}" class="whitespace-nowrap font-bold text-[#febd69] px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded">Sell on Adamawa Ecommerce platform</a>
                @endhasrole
                <div class="flex-1"></div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="whitespace-nowrap font-medium px-2 py-1 hover:outline hover:outline-1 hover:outline-white/30 rounded flex items-center gap-1">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-[1500px] mx-auto px-4 py-6">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-[#232f3e] text-white text-xs">
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
                &copy; {{ date('Y') }} Adamawa Ecommerce platform. All rights reserved.
            </div>
        </footer>
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
        <div class="bg-white rounded-xl shadow-2xl border border-slate-100 p-4 flex items-center gap-4">
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

        function performSearch() {
            const query = document.getElementById('search-input')?.value;
            if (query) {
                window.location.href = '{{ route("buyer.products.index") }}?search=' + encodeURIComponent(query);
            }
        }

        document.getElementById('search-input')?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') performSearch();
        });

        document.addEventListener('livewire:navigated', () => lucide.createIcons());
    </script>
    @stack('scripts')
</body>
</html>
