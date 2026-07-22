<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FulfillmentInventory;
use App\Models\SellerProfile;
use App\Models\Product;
use App\Models\AtexAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $records = FulfillmentInventory::with(['sellerProfile', 'product'])->latest()->get();
        $sellers = SellerProfile::all();
        $products = Product::where('status', 'approved')->get();
        $units = \App\Models\UnitOfMeasurement::where('status', true)->get();

        return view('admin.inventory.index', compact('records', 'sellers', 'products', 'units'));
    }

    public function receive(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'seller_profile_id' => 'required|exists:seller_profiles,id',
            'product_id' => 'required|exists:products,id',
            'quantity_received' => 'required|integer|min:1',
            'unit_label' => 'required|string|max:50',
            'storage_location' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        $inv = FulfillmentInventory::create([
            'seller_profile_id' => $request->seller_profile_id,
            'product_id' => $request->product_id,
            'brand_name' => $product->brand_name,
            'seller_sku' => $product->seller_sku,
            'quantity_received' => $request->quantity_received,
            'quantity_available' => $request->quantity_received,
            'unit_label' => $request->unit_label,
            'storage_location' => $request->storage_location,
            'notes' => $request->notes,
            'received_at' => now(),
        ]);

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'received_inventory_lot',
            'auditable_type' => 'fulfillment_inventory',
            'auditable_id' => $inv->id,
            'new_values' => json_encode(['quantity_received' => $request->quantity_received]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.inventory.index')->with('success', 'Inventory lot recorded successfully.');
    }
}

