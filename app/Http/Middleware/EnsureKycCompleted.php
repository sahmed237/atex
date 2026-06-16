<?php

namespace App\Http\Middleware;

use App\Models\ExporterProfile;
use App\Models\BuyerProfile;
use App\Models\LogisticsProfile;
use App\Models\FieldOfficerProfile;
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
            'exporter' => [ExporterProfile::class, 'exporter'],
            'buyer' => [BuyerProfile::class, 'buyer'],
            'logistics' => [LogisticsProfile::class, 'logistics'],
            'field-officer' => [FieldOfficerProfile::class, 'field-officer'],
        ];

        foreach ($roleProfileMap as $role => [$modelClass, $label]) {
            if ($user->hasRole($role)) {
                $profile = $modelClass::where('user_id', $user->id)->first();
                if (!$profile || in_array($profile->verification_status, ['pending', 'rejected'])) {
                    return redirect()->route('kyc.onboarding');
                }
                break;
            }
        }

        return $next($request);
    }
}
