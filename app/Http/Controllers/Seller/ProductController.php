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
        
        $categories = Category::where('status', true)->get();
        $packagings = \App\Models\ProductPackaging::where('status', true)->get();
        $units = \App\Models\UnitOfMeasurement::where('status', true)->get();

        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $products = Product::with(['category', 'sellerProfile'])->latest()->get();
            return view('seller.products.admin', compact('products', 'categories', 'packagings', 'units'));
        }

        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            $products = Product::where('seller_profile_id', $profile->id ?? 0)->with('category')->latest()->get();
            return view('seller.products.seller', compact('products', 'categories', 'profile', 'packagings', 'units'));
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
            'moq_value' => 'required|numeric|min:0',
            'moq_unit' => 'required|string|max:50',
            'available_quantity_value' => 'nullable|numeric|min:0',
            'available_quantity_unit' => 'nullable|string|max:50',
            'origin_lga' => 'required|string|max:255',
            'product_image' => 'nullable|image|max:2048',
            'packaging' => 'required|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
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

        $moqCombined = $request->moq_value . ' ' . $request->moq_unit;
        $availableQtyCombined = $request->filled('available_quantity_value') 
            ? $request->available_quantity_value . ' ' . $request->available_quantity_unit 
            : null;

        Product::create([
            'seller_profile_id' => $profile->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'hs_code' => $request->hs_code,
            'moq' => $moqCombined,
            'available_quantity' => $availableQtyCombined,
            'unit_price' => $request->unit_price ?: 'Request quote',
            'image_path' => $imagePath,
            'seller_sku' => $request->seller_sku ?: $sku,
            'brand_name' => $request->brand_name ?: $brandName,
            'fulfillment_model' => $request->fulfillment_mode ?: 'seller_direct',
            'fulfillment_eligible' => $request->fulfillment_eligible ? true : false,
            'quote_required' => $profile->seller_tier === 'export',
            'packaging' => $request->packaging,
            'weight' => $request->weight,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
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

    public function show($id)
    {
        $user = Auth::user();
        
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $product = Product::with(['category', 'sellerProfile'])->findOrFail($id);
            $profile = $product->sellerProfile;
        } else {
            $profile = SellerProfile::where('user_id', $user->id)->firstOrFail();
            $product = Product::with(['category', 'sellerProfile'])
                ->where('seller_profile_id', $profile->id)
                ->findOrFail($id);
        }

        $inventoryLots = \App\Models\FulfillmentInventory::where('product_id', $product->id)->latest()->get();

        return view('seller.products.show', compact('product', 'profile', 'inventoryLots'));
    }
}

