@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<style>
    /* Custom specific styles to match Adamawa Ecommerce platform perfectly */
    .login-hero-bg {
        background: linear-gradient(90deg, rgba(11, 35, 25, 0.92), rgba(11, 35, 25, 0.55)), url("https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=1600&q=80") center/cover;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0f5132 0%, #1f7a4d 100%);
        box-shadow: 0 12px 26px rgba(15, 81, 50, 0.2);
        color: white;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    }
    .btn-primary:hover {
        transform: translateY(-1px);
    }
    .btn-secondary {
        color: #0f5132;
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid #d7e3da;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease;
    }
    .btn-secondary:hover {
        transform: translateY(-1px);
        background: #f8fbf8;
    }
    
    .form-input-custom {
        border: 1px solid #d7e3da;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.8);
        transition: all 0.2s;
    }
    .form-input-custom:focus {
        border-color: rgba(31, 122, 77, 0.48);
        box-shadow: 0 0 0 4px rgba(31, 122, 77, 0.1);
        outline: none;
    }
</style>

<div class="min-h-screen flex flex-col lg:grid lg:grid-cols-[1fr_430px]">
    
    <!-- Hero Section -->
    <section class="login-hero-bg flex flex-col justify-end p-8 lg:p-16 xl:p-20 text-white min-h-[320px] lg:min-h-screen">
        <span class="text-[#c99724] text-xs font-[850] uppercase tracking-wider mb-4 block">Enterprise Application</span>
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-4 max-w-3xl" style="line-height: 0.95;">
            Verified export trade operations for Adamawa State.
        </h1>
        <p class="text-white/80 text-lg max-w-2xl">
            Manage sellers, products, RFQs, compliance documents, orders, payments, logistics, and audit trails from one role-aware system.
        </p>
    </section>

    <!-- Login Panel -->
    <section class="bg-white/98 flex flex-col justify-center p-8 lg:p-10 shadow-[-20px_0_40px_rgba(0,0,0,0.05)] relative z-10">
        
        <div class="flex items-center gap-3 mb-8">
            <div class="flex items-center justify-center w-10 h-10 bg-white border border-[#d7e3da] rounded-lg overflow-hidden shrink-0">
                @if(!empty($system_settings['platform_logo']))
                    <img src="{{ $system_settings['platform_logo'] }}" alt="Logo" class="max-w-[28px] max-h-[28px] object-contain">
                @else
                    <span class="text-[#0f5132] font-black text-xl">{{ substr($system_settings['platform_name'] ?? 'APP', 0, 1) }}</span>
                @endif
            </div>
            <div>
                <strong class="block text-[#17201c] font-bold text-lg leading-tight">{{ $system_settings['platform_name'] ?? 'Adamawa Ecommerce platform' }}</strong>
                <small class="block text-[#65736b] text-sm">Enterprise login</small>
            </div>
        </div>

        @if($errors->any())
            <div class="p-3 mb-4 bg-[#fde8e6] text-[#b83a35] rounded-lg text-sm font-medium border border-[#b83a35]/20">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="p-3 mb-4 bg-[#e6f4ec] text-[#17633c] rounded-lg text-sm font-medium border border-[#17633c]/20">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            
            <div class="flex flex-col gap-2">
                <label class="text-[#17201c] font-[750] text-sm">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="form-input-custom w-full min-h-[42px] px-3 py-2 rounded-lg text-[#17201c] bg-white"
                    placeholder="admin@example.com">
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[#17201c] font-[750] text-sm">Password</label>
                <input type="password" name="password" required
                    class="form-input-custom w-full min-h-[42px] px-3 py-2 rounded-lg text-[#17201c] bg-white"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-[#1f7a4d] border-[#d7e3da] rounded focus:ring-[#1f7a4d]">
                <label for="remember" class="ml-2 block text-sm font-medium text-[#65736b] !mb-0 !font-normal">Remember me</label>
            </div>

            <button type="submit" class="btn-primary w-full min-h-[40px] px-4 rounded-lg font-[800] text-sm flex items-center justify-center mt-2">
                Sign In
            </button>
        </form>

        <div class="flex flex-wrap gap-2.5 mt-4">
            <a href="/" class="btn-secondary flex-1 min-h-[40px] px-4 rounded-lg font-[800] text-sm flex items-center justify-center">Back to Landing</a>
            <a href="/register" class="btn-secondary flex-1 min-h-[40px] px-4 rounded-lg font-[800] text-sm flex items-center justify-center">Create Account</a>
        </div>

        <div class="flex flex-col gap-2 mt-6 p-4 bg-[#f8fbf8] rounded-lg border border-[#d7e3da]/50">
            <strong class="text-[#17201c] text-sm">Seeded demo accounts</strong>
            <small class="text-[#65736b] text-xs">admin@example.com, officer@example.com, seller@example.com, buyer@example.com, logistics@example.com</small>
            <small class="text-[#65736b] text-xs">Password for all accounts: password</small>
        </div>

    </section>
</div>
@endsection