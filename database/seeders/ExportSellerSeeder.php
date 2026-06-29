<?php

namespace Database\Seeders;

use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExportSellerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'mybbu@atex.com'],
            [
                'name' => 'My BBu',
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('seller')) {
            $user->assignRole('seller');
        }

        $seller = SellerProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'business_name' => 'My BBu',
                'seller_tier' => 'export',
                'verification_status' => 'approved',
                'seller_program_status' => 'active',
            ]
        );

        $exportProductIds = \App\Models\Product::orderBy('id')->skip(6)->take(5)->pluck('id');

        \App\Models\Product::whereIn('id', $exportProductIds)
            ->where('seller_profile_id', '!=', $seller->id)
            ->update(['seller_profile_id' => $seller->id]);

        \App\Models\Product::where('seller_profile_id', $seller->id)
            ->update(['quote_required' => true]);

        \App\Models\Product::where('seller_profile_id', '!=', $seller->id)
            ->update(['quote_required' => false]);
    }
}
