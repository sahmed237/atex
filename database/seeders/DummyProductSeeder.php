<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;

class DummyProductSeeder extends Seeder
{
    public function run()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'demo.seller@atex.com'],
            [
                'name' => 'Demo Seller',
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
                'business_name' => 'Adamawa Trade Co.',
                'verification_status' => 'approved',
                'seller_program_status' => 'active',
                'lga' => 'Yola North',
            ]
        );

        $categories = Category::where('status', true)->get();
        if ($categories->isEmpty()) return;

        $products = [
            [
                'name' => 'Premium Export-Grade Sesame Seeds',
                'brand_name' => 'Adamawa Harvests',
                'description' => 'High quality, machine-cleaned white sesame seeds. Perfect for oil extraction or direct consumption. Contains high oil content with minimal impurities.',
                'origin_lga' => 'Yola North',
                'moq' => '20 Metric Tons',
                'unit_price' => '850000',
                'readiness_score' => 95,
                'hs_code' => '120740',
                'packaging' => '50kg Jute Bags',
            ],
            [
                'name' => 'Organic Hibiscus Sabdariffa (Zobo) Leaves',
                'brand_name' => 'Pure Tropics',
                'description' => 'Sun-dried premium organic hibiscus flowers. Hand-picked and sorted to guarantee zero sand and stones. Deep crimson color, perfect for tea and beverage manufacturing.',
                'origin_lga' => 'Mubi South',
                'moq' => '5 Metric Tons',
                'unit_price' => '600000',
                'readiness_score' => 90,
                'hs_code' => '121190',
                'packaging' => '25kg PP Bags',
            ],
            [
                'name' => 'Raw Unprocessed Shea Butter',
                'brand_name' => 'Savannah Naturals',
                'description' => 'Grade A unrefined shea butter sourced directly from local cooperatives. Rich in vitamins A and E, ideal for cosmetics and skincare formulations.',
                'origin_lga' => 'Ganye',
                'moq' => '1000 Kilograms',
                'unit_price' => '2500',
                'readiness_score' => 88,
                'hs_code' => '151590',
                'packaging' => '20kg Cartons',
            ],
            [
                'name' => 'Traditional Hand-Woven Aso-Oke Fabric',
                'brand_name' => 'Heritage Textiles',
                'description' => 'Authentic, intricately patterned Aso-Oke woven by master artisans. Available in various traditional and contemporary color palettes. Made with 100% natural cotton.',
                'origin_lga' => 'Yola South',
                'moq' => '50 Yards',
                'unit_price' => '15000',
                'readiness_score' => 85,
                'hs_code' => '520852',
                'packaging' => 'Bales',
            ],
            [
                'name' => 'Sun-Dried Split Ginger',
                'brand_name' => 'Adamawa Spices',
                'description' => 'Highly pungent and aromatic dried split ginger. Cleaned and dried to international moisture standards. Excellent for culinary and medicinal uses.',
                'origin_lga' => 'Mayo-Belwa',
                'moq' => '10 Metric Tons',
                'unit_price' => '950000',
                'readiness_score' => 92,
                'hs_code' => '091011',
                'packaging' => '40kg PP Bags',
            ],
            [
                'name' => 'Hardwood Charcoal (Restaurant Grade)',
                'brand_name' => 'EcoBurn',
                'description' => 'High carbon, low ash hardwood charcoal. Smokeless, sparkless, and long-burning. Sustainably sourced from authorized forestry operations.',
                'origin_lga' => 'Song',
                'moq' => '40ft HC Container',
                'unit_price' => '120000',
                'readiness_score' => 98,
                'hs_code' => '440290',
                'packaging' => '10kg Paper Bags',
            ],
            [
                'name' => 'Premium Cashew Nuts In Shell (Raw)',
                'brand_name' => 'Savannah Nuts',
                'description' => 'High-yield raw cashew nuts with excellent out-turn ratio. Sun-dried and carefully graded to ensure premium quality kernel extraction.',
                'origin_lga' => 'Gombi',
                'moq' => '15 Metric Tons',
                'unit_price' => '450000',
                'readiness_score' => 82,
                'hs_code' => '080131',
                'packaging' => '80kg Jute Bags',
            ],
            [
                'name' => 'Solid Lead Ore (Galena)',
                'brand_name' => 'Adamawa Minerals Co.',
                'description' => 'High-grade lead ore with purity levels above 60%. Industrially verified and ready for smelting or export. Fully licensed mining operation.',
                'origin_lga' => 'Guyuk',
                'moq' => '50 Metric Tons',
                'unit_price' => '1150000',
                'readiness_score' => 80,
                'hs_code' => '260700',
                'packaging' => 'Bulk / 50kg Bags',
            ],
            [
                'name' => 'Dried Ginger Slices',
                'brand_name' => null,
                'description' => 'Premium sun-dried ginger slices sourced from organic farms. Strong aroma and high oleoresin content.',
                'origin_lga' => 'Mubi South',
                'moq' => '3 Metric Tons',
                'unit_price' => '450000',
                'readiness_score' => 70,
                'hs_code' => '091011',
                'packaging' => null,
            ],
            [
                'name' => 'Raw Shea Butter (Grade A)',
                'brand_name' => null,
                'description' => 'Unrefined Grade A shea butter with high fatty acid content. Ideal for cosmetic and pharmaceutical applications.',
                'origin_lga' => 'Numan',
                'moq' => '2 Metric Tons',
                'unit_price' => '320000',
                'readiness_score' => 75,
                'hs_code' => '151590',
                'packaging' => null,
            ],
            [
                'name' => 'Sesame Seeds (White)',
                'brand_name' => null,
                'description' => 'Premium white sesame seeds with high oil content. Machine-cleaned and sorted for export quality.',
                'origin_lga' => 'Yola North',
                'moq' => '15 Metric Tons',
                'unit_price' => '780000',
                'readiness_score' => 85,
                'hs_code' => '120740',
                'packaging' => null,
            ],
        ];

        if (\App\Models\Product::count() > 0) return;

        foreach ($products as $prod) {
            $prod['seller_profile_id'] = $seller->id;
            $prod['category_id'] = $categories->random()->id;
            $prod['status'] = 'approved';
            $prod['quote_required'] = false;
            Product::create($prod);
        }
    }
}
