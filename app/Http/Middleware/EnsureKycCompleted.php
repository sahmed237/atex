<?php

namespace App\Http\Middleware;

use App\Models\SellerProfile;
use App\Models\BuyerProfile;
use App\Models\LogisticsProfile;
use App\Models\AdminProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKycCompleted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Bypass KYC for super admins
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        $roleProfileMap = [
            'seller' => [SellerProfile::class, 'seller'],
            'buyer' => [BuyerProfile::class, 'buyer'],
            'logistics' => [LogisticsProfile::class, 'logistics'],
            'admin' => [AdminProfile::class, 'admin'],
        ];

        foreach ($roleProfileMap as $role => [$modelClass, $label]) {
            if ($user->hasRole($role)) {
                $profile = $modelClass::where('user_id', $user->id)->first();
                if (!$profile || in_array($profile->verification_status, ['pending', 'rejected'])) {
                    // Allow local sellers who are pending export upgrade to still access the dashboard
                    if ($role === 'seller' && $profile && $profile->seller_tier === 'export' && $profile->verification_status === 'pending') {
                        // Do not redirect
                    } else {
                        return redirect()->route('kyc.onboarding');
                    }
                }
                break;
            }
        }

        return $next($request);
    }
}
