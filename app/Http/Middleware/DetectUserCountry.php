<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Stevebauman\Location\Facades\Location;
use Symfony\Component\HttpFoundation\Response;

class DetectUserCountry
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('user_country') || !session()->has('user_currency')) {
            $ip = $request->ip();

            if (app()->environment('local') && env('TEST_USER_IP')) {
                $ip = env('TEST_USER_IP');
            }

            $location = Location::get($ip);

            if ($location && !empty($location->countryCode)) {
                $countryCode = strtoupper($location->countryCode);
                $countryName = $location->countryName ?: $countryCode;
            } else {
                $countryCode = 'NG';
                $countryName = 'Nigeria';
            }

            $currency = $this->mapCountryToCurrency($countryCode);

            session([
                'user_country' => $countryCode,
                'user_country_name' => $countryName,
                'user_currency' => $currency,
            ]);
        }

        View::share('user_country', session('user_country', 'NG'));
        View::share('user_country_name', session('user_country_name', 'Nigeria'));
        View::share('user_currency', session('user_currency', 'NGN'));

        return $next($request);
    }

    /**
     * Map country code to default trading currency.
     */
    private function mapCountryToCurrency(string $countryCode): string
    {
        if ($countryCode === 'NG') {
            return 'NGN';
        }

        $eurCountries = [
            'GB', 'FR', 'DE', 'IT', 'ES', 'NL', 'BE', 'AT', 'IE', 'FI', 
            'PT', 'GR', 'CY', 'EE', 'LV', 'LT', 'LU', 'MT', 'SK', 'SI'
        ];

        if (in_array($countryCode, $eurCountries, true)) {
            return 'EUR';
        }

        return 'USD';
    }
}
