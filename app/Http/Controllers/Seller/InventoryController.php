<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->hasRole('seller')) {
            return redirect()->back();
        }

        $profile = \App\Models\SellerProfile::where('user_id', $user->id)->first();
        $records = [];
        if ($profile) {
            $records = \App\Models\FulfillmentInventory::where('seller_profile_id', $profile->id)
                ->with('product')
                ->latest()
                ->get();
        }
        
        return view('seller.inventory.index', compact('records', 'profile'));
    }
}
