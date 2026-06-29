<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingSeeder::class,
            AtexDemoSeeder::class,
            BusinessCategorySeeder::class,
            ProductPackagingSeeder::class,
            UnitOfMeasurementSeeder::class,
            CategorySeeder::class,
            DummyProductSeeder::class,
            ExportSellerSeeder::class,
        ]);

        $user = User::firstOrCreate(
            ['email' => 'superadmin@atex.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('superadmin'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }
    }
}
