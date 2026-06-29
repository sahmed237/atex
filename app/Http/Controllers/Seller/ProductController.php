<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SellerProfile;
use App\Models\AtexAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $products = Product::with(['category', 'sellerProfile'])->latest()->get();
            $categories = Category::where('status', 'active')->get();
            return view('seller.products.admin', compact('products', 'categories'));
        }

        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            $products = Product::where('seller_profile_id', $profile->id ?? 0)->with('category')->latest()->get();
            $categories = Category::where('status', 'active')->get();
            return view('seller.products.seller', compact('products', 'categories', 'profile'));
        }

        return redirect()->route('admin.dashboard');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('seller')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $profile = SellerProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return redirect()->back()->with('error', 'Seller profile not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'moq' => 'required|string|max:100',
            'origin_lga' => 'required|string|max:255',
            'product_image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('product_image')) {
            $file = $request->file('product_image');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads/products'), $fileName);
            $imagePath = 'storage/uploads/products/' . $fileName;
        }

        $brandName = $profile->seller_brand_name ?: $profile->business_name;
        $sku = 'AEM-' . time();

        Product::create([
            'seller_profile_id' => $profile->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'hs_code' => $request->hs_code,
            'moq' => $request->moq,
            'available_quantity' => $request->available_quantity,
            'unit_price' => $request->unit_price ?: 'Request quote',
            'image_path' => $imagePath,
            'seller_sku' => $request->seller_sku ?: $sku,
            'brand_name' => $request->brand_name ?: $brandName,
            'fulfillment_mode' => $request->fulfillment_mode ?: 'seller_direct',
            'fulfillment_eligible' => $request->fulfillment_eligible ? true : false,
            'quote_required' => $profile->seller_tier === 'export',
            'packaging' => $request->packaging,
            'origin_lga' => $request->origin_lga,
            'status' => 'pending_review',
        ]);

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'created_product',
            'auditable_type' => 'product',
            'auditable_id' => 0, // Placeholder, resolved in DB
            'new_values' => json_encode(['name' => $request->name]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Product listing submitted successfully and is pending review.');
    }

    public function review(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $product = Product::findOrFail($id);
        $oldStatus = $product->status;
        $product->update([
            'status' => $request->status,
        ]);

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'reviewed_product',
            'auditable_type' => 'product',
            'auditable_id' => $product->id,
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode(['status' => $request->status]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product has been ' . $request->status . '.');
    }
}

