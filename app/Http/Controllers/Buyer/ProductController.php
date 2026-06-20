<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Product::with(['category', 'sellerProfile'])
            ->where('status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price_asc' => $query->orderBy('unit_price', 'asc'),
                'price_desc' => $query->orderBy('unit_price', 'desc'),
                'name' => $query->orderBy('name', 'asc'),
                default => $query->latest(),
            };
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('status', 'active')->get();

        return view('buyer.products.index', compact('products', 'categories', 'user'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'sellerProfile'])
            ->where('status', 'approved')
            ->findOrFail($id);

        $related = Product::with(['category', 'sellerProfile'])
            ->where('status', 'approved')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('buyer.products.show', compact('product', 'related'));
    }
}
