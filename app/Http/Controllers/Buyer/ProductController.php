<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $countryCode = session('user_country', 'NG');

        $query = Product::with(['category', 'sellerProfile'])
            ->whereIn('status', ['approved', 'pending_review', 'active', 'published']);

        if ($countryCode !== 'NG') {
            $query->whereHas('sellerProfile', function ($q) {
                $q->where('seller_tier', 'export');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('origin_lga', 'like', "%{$search}%")
                  ->orWhere('hs_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $categories = (array) $request->category;
            $categories = array_filter($categories);
            if (!empty($categories)) {
                $query->whereHas('category', function($q) use ($categories) {
                    $q->whereIn('slug', $categories);
                });
            }
        }

        if ($request->filled('min_price')) {
            $query->where('unit_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('unit_price', '<=', $request->max_price);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc', 'price-asc' => $query->orderBy('unit_price', 'asc'),
                'price_desc', 'price-desc' => $query->orderBy('unit_price', 'desc'),
                'name' => $query->orderBy('name', 'asc'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view('buyer.products.index', compact('products', 'user'));
    }

    public function categories()
    {
        $allCategories = Category::where('status', true)->orderBy('name')->get();
        return view('buyer.products.categories', compact('allCategories'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'sellerProfile'])
            ->whereIn('status', ['approved', 'pending_review', 'active', 'published'])
            ->findOrFail($id);

        $countryCode = session('user_country', 'NG');
        $related = Product::with(['category', 'sellerProfile'])
            ->whereIn('status', ['approved', 'pending_review', 'active', 'published'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id);

        if ($countryCode !== 'NG') {
            $related->whereHas('sellerProfile', function ($q) {
                $q->where('seller_tier', 'export');
            });
        }

        $related = $related->latest()->take(4)->get();

        $reviews = ProductReview::with('user')
            ->where('product_name', $product->name)
            ->latest()
            ->get();
        $avgRating = $reviews->avg('rating') ?? 0;

        return view('buyer.products.show', compact('product', 'related', 'reviews', 'avgRating'));
    }
}
