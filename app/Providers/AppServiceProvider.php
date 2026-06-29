<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(125);

        // Configure Mailer from Settings
        if (Schema::hasTable('settings')) {
            \App\Models\Setting::configureMailer();
        }

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Share settings & categories with all views
        view()->composer('*', function ($view) {
            if (Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
                $view->with('system_settings', $settings);
            }
            $view->with('sharedCategories', \Illuminate\Support\Facades\Cache::remember('categories.active', 3600, function () {
                return \App\Models\Category::where('status', true)->get();
            }));
        });

        // Authentication Logging
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            \App\Models\AuthenticationLog::log($event->user, 'login');
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                \App\Models\AuthenticationLog::log($event->user, 'logout');
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Failed::class, function ($event) {
            if ($event->user) {
                \App\Models\AuthenticationLog::log($event->user, 'failed_login', [
                    'email' => $event->credentials['email'] ?? 'Unknown'
                ]);
            }
        });

        // Polymorphic morph map for compliance documents
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'seller' => \App\Models\SellerProfile::class,
            'buyer' => \App\Models\BuyerProfile::class,
            'logistics' => \App\Models\LogisticsProfile::class,
            'product' => \App\Models\Product::class,
        ]);
    }
}
