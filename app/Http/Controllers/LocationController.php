<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function setCountry(Request $request)
    {
        $validated = $request->validate([
            'country' => 'required|string|size:2',
            'country_name' => 'required|string',
            'currency' => 'required|string|size:3',
        ]);

        session([
            'user_country' => strtoupper($validated['country']),
            'user_country_name' => $validated['country_name'],
            'user_currency' => strtoupper($validated['currency']),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'country' => session('user_country'),
                'country_name' => session('user_country_name'),
                'currency' => session('user_currency'),
            ]);
        }

        return redirect()->back()->with('status', 'Location updated successfully.');
    }
}
