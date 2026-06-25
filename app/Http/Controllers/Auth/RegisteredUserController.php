<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BuyerProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $passwordSettings = \App\Models\Setting::where('group', 'security')
            ->whereIn('key', [
                'password_min_length',
                'password_require_uppercase',
                'password_require_lowercase',
                'password_require_number',
                'password_require_special'
            ])->pluck('value', 'key')->toArray();

        $legalDocuments = \App\Models\LegalDocument::with('activeVersion')
            ->whereHas('activeVersion')
            ->get();

        return view('auth.register', compact('passwordSettings', 'legalDocuments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $passwordSettings = \App\Models\Setting::where('group', 'security')
            ->whereIn('key', [
                'password_min_length',
                'password_require_uppercase',
                'password_require_lowercase',
                'password_require_number',
                'password_require_special'
            ])->pluck('value', 'key')->toArray();

        $passwordRule = Rules\Password::min((int) ($passwordSettings['password_min_length'] ?? 8));
        
        if (($passwordSettings['password_require_uppercase'] ?? false) && ($passwordSettings['password_require_lowercase'] ?? false)) {
            $passwordRule->mixedCase();
        } elseif ($passwordSettings['password_require_uppercase'] ?? false || $passwordSettings['password_require_lowercase'] ?? false) {
            $passwordRule->letters();
        }

        if ($passwordSettings['password_require_number'] ?? false) {
            $passwordRule->numbers();
        }
        if ($passwordSettings['password_require_special'] ?? false) {
            $passwordRule->symbols();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', $passwordRule],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Assign the role
        $user->assignRole('buyer');

        // Accept active legal documents implicitly
        $legalDocuments = \App\Models\LegalDocument::with('activeVersion')->whereHas('activeVersion')->get();
        foreach ($legalDocuments as $doc) {
            \App\Models\UserDocumentAcceptance::create([
                'user_id' => $user->id,
                'legal_document_version_id' => $doc->activeVersion->id,
                'accepted_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false));
    }
}
