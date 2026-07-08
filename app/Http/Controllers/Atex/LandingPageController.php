<?php

namespace App\Http\Controllers\Atex;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $countryCode = session('user_country', 'NG');

        $cacheKey = 'landing.marketplaceProducts.' . $countryCode;
        $marketplaceProducts = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($countryCode) {
            $query = Product::where('status', 'approved')
                ->with(['sellerProfile', 'category'])
                ->latest();

            if ($countryCode !== 'NG') {
                $query->whereHas('sellerProfile', function ($q) {
                    $q->where('seller_tier', 'export');
                });
            }

            $products = $query->get();

            $emojis = ['🎧', '👜', '☕', '🔊', '🧵', '🥭', '🔋', '🧺', '🔌', '🧣', '🍵', '🌾', '📦'];
            $tags = [null, 'Bestseller', 'Sale', 'New'];

            return $products->map(function ($product) use ($emojis, $tags) {
                $price = is_numeric($product->unit_price) ? (float) $product->unit_price : 0;

                return [
                    'id' => (int) $product->id,
                    'name' => $product->name,
                    'category' => $product->category->slug ?? 'general',
                    'seller' => $product->sellerProfile->business_name ?? 'Verified Seller',
                    'origin' => ($product->origin_lga ?: 'Adamawa') . ', Adamawa',
                    'moq' => $product->moq,
                    'price' => $price,
                    'oldPrice' => null,
                    'emoji' => $emojis[$product->id % count($emojis)],
                    'tag' => $tags[$product->id % count($tags)],
                    'rating' => 4.5,
                    'image' => $product->image_path ? asset($product->image_path) : $this->marketplaceProductImage($product->category->name ?? ''),
                    'type' => $product->isExport() ? 'export' : 'local',
                ];
            });
        });

        $productCount = \Illuminate\Support\Facades\Cache::remember('landing.productCount.' . $countryCode, 300, function () use ($countryCode) {
            $query = Product::where('status', 'approved');
            if ($countryCode !== 'NG') {
                $query->whereHas('sellerProfile', function ($q) {
                    $q->where('seller_tier', 'export');
                });
            }
            return $query->count();
        });

        return view('welcome', compact('user', 'marketplaceProducts', 'productCount'));
    }

    private function marketplaceProductImage(string $category): string
    {
        $images = [
            'Agriculture' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=900&q=80',
            'Agricultural Produce' => 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&w=900&q=80',
            'Food Processing' => 'https://images.unsplash.com/photo-1611071526480-f6f8613f7d4b?auto=format&fit=crop&w=900&q=80',
            'Textiles' => 'https://images.unsplash.com/photo-1528404021824-577c0f3b0f4a?auto=format&fit=crop&w=900&q=80',
            'Minerals' => 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?auto=format&fit=crop&w=900&q=80',
        ];

        return $images[$category] ?? 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?auto=format&fit=crop&w=900&q=80';
    }
}
