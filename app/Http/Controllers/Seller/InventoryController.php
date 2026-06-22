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
        
        // Render a basic placeholder for now
        return view('seller.inventory.index');
    }
}
