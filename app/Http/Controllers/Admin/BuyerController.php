<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use App\Mail\UserWelcomeMail;
use App\Mail\EmailVerifiedMail;
use App\Mail\UserPasswordResetMail;
use App\Mail\GeneralUserMail;

class BuyerController extends Controller
{
    public function index(Request $request)
    {
        $query = Buyer::query();

        // View Filter (Normal vs Trash)
        if ($request->get('view') === 'trash') {
            $query->onlyTrashed();
        }

        // Exclude Super Admin from list
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super-admin');
        });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'suspended') {
                $query->where('is_active', false);
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $buyers = $query->with('roles')->paginate(10)->withQueryString();

        // Stats
        $stats = [
            'total' => Buyer::whereDoesntHave('roles', fn($q) => $q->where('name', 'super-admin'))->count(),
            'active' => Buyer::where('is_active', true)->whereDoesntHave('roles', fn($q) => $q->where('name', 'super-admin'))->count(),
            'suspended' => Buyer::where('is_active', false)->whereDoesntHave('roles', fn($q) => $q->where('name', 'super-admin'))->count(),
            'trashed' => Buyer::onlyTrashed()->count(),
        ];

        return view('admin.buyers.index', compact('buyers', 'stats'));
    }

    public function show(Buyer $buyer)
    {
        return view('admin.buyers.show', compact('buyer'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        return view('admin.buyers.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'passport' => 'nullable|image|max:2048',
            'password' => 'required|string|min:8',
            'roles' => 'required|array'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'require_password_change' => true,
        ];

        if ($request->hasFile('passport')) {
            $path = $request->file('passport')->store('passports', 'public');
            $data['passport'] = asset('storage/' . $path);
        }

        $buyer = Buyer::create($data);

        $buyer->assignRole($request->roles);

        // Generate Verification Link (Expires in 48 hours)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(48),
            ['id' => $buyer->id, 'hash' => sha1($buyer->getEmailForVerification())]
        );

        // Send Welcome Email
        try {
            \App\Models\Setting::configureMailer();
            Mail::to($buyer->email)->send(new UserWelcomeMail($user, $request->password, $verificationUrl));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.buyers.index')->with('success', 'Buyer created successfully and notification sent.');
    }

    /**
     * Show verification notice page
     */
    public function showVerificationNotice(Request $request)
    {
        return view('auth.verify-notice', ['email' => $request->email]);
    }

    /**
     * Resend verification link (Public)
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $buyer = Buyer::where('email', $request->email)->first();

        if (! $user) {
            return back()->with('error', 'No account found with this email address.');
        }

        if ($buyer->hasVerifiedEmail()) {
            return redirect()->route('login')->with('info', 'Email already verified. Please login.');
        }

        // Check if user is allowed to request verification
        $canRequest = \App\Models\Setting::get('user_can_request_new_email_verification', '1') == '1';
        if (! $canRequest) {
            return back()->with('error', 'Self-service verification requests are currently disabled. Please contact support.');
        }

        $this->sendVerificationLink($user);

        return back()->with('success', 'A new verification link has been sent to your email address.');
    }

    /**
     * Resend verification link (Admin)
     */
    public function resendVerificationAdmin($id)
    {
        $buyer = Buyer::findOrFail($id);

        if ($buyer->hasVerifiedEmail()) {
            return back()->with('error', 'User is already verified.');
        }

        $this->sendVerificationLink($user);

        return back()->with('success', "New verification link sent to {$buyer->name}.");
    }

    /**
     * Reset 2FA for user
     */
    public function resetTwoFactor($id)
    {
        $buyer = Buyer::findOrFail($id);

        $buyer->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        \App\Models\AuthenticationLog::log($user, '2fa_reset', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name
        ]);

        return back()->with('success', "Two-factor authentication for {$buyer->name} has been reset.");
    }

    /**
     * Helper to generate and send link
     */
    private function sendVerificationLink($user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(48),
            ['id' => $buyer->id, 'hash' => sha1($buyer->getEmailForVerification())]
        );

        try {
            \App\Models\Setting::configureMailer();
            Mail::to($buyer->email)->send(new \App\Mail\ResendVerificationMail($user, $verificationUrl));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Resend Mail failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle email verification from link
     */
    public function verifyEmailLink(Request $request, $id, $hash)
    {
        if (! $request->hasValidSignature()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'The verification link has expired or is invalid.'], 403);
            }
            abort(403, 'The verification link has expired or is invalid.');
        }

        $buyer = Buyer::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($buyer->getEmailForVerification()))) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Invalid verification hash.'], 403);
            }
            abort(403, 'Invalid verification hash.');
        }

        // Handle GET Request: Show Animated Processing Page
        if ($request->isMethod('GET')) {
            return view('auth.verify-processing', [
                'verifyUrl' => URL::full()
            ]);
        }

        // Handle POST Request: Perform Actual Verification
        if ($buyer->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        if ($buyer->markEmailAsVerified()) {
            \App\Models\AuthenticationLog::log($user, 'email_verified', ['via' => 'signed_link']);

            // Send Confirmation Email
            try {
                \App\Models\Setting::configureMailer();
                Mail::to($buyer->email)->send(new EmailVerifiedMail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Confirmation Mail failed: ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Email verified successfully!']);
    }

    public function edit(Buyer $buyer)
    {
        $roles = Role::where('name', '!=', 'super-admin')->get();
        return view('admin.buyers.edit', compact('buyer', 'roles'));
    }

    public function update(Request $request, Buyer $buyer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $buyer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'passport' => 'nullable|image|max:2048',
            'roles' => 'required|array'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->hasFile('passport')) {
            if ($buyer->passport) {
                $oldPath = str_replace(asset('storage/'), '', $buyer->passport);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('passport')->store('passports', 'public');
            $data['passport'] = asset('storage/' . $path);
        }

        $buyer->update($data);

        if ($request->password) {
            $buyer->update(['password' => Hash::make($request->password)]);
        }

        $buyer->syncRoles($request->roles);

        return redirect()->route('admin.buyers.index')->with('success', 'Buyer profile updated successfully.');
    }

    public function becomeSeller($id)
    {
        $user = User::findOrFail($id);

        if (!$user->hasRole('buyer')) {
            return redirect()->back()->with('error', 'This user is not a buyer.');
        }

        $user->removeRole('buyer');
        $user->assignRole('seller');

        return redirect()->back()->with('success', "{$user->name} has been promoted to Seller.");
    }

    public function destroy(Buyer $buyer)
    {
        if (auth()->id() === $buyer->id) {
            return redirect()->route('admin.buyers.index')->with('error', 'You cannot delete your own account.');
        }

        $buyer->delete();
        return redirect()->route('admin.buyers.index')->with('success', 'Buyer moved to trash.');
    }

    public function toggleStatus($id)
    {
        $buyer = Buyer::findOrFail($id);
        $buyer->update(['is_active' => !$buyer->is_active]);

        $status = $buyer->is_active ? 'activated' : 'suspended';
        return redirect()->back()->with('success', "Account for {$buyer->name} has been {$status} successfully.");
    }

    public function resendWelcome($id)
    {
        $buyer = Buyer::findOrFail($id);
        $password = \Illuminate\Support\Str::random(10);
        $buyer->update(['password' => Hash::make($password), 'require_password_change' => true]);

        try {
            \App\Models\Setting::configureMailer();
            Mail::to($buyer->email)->send(new UserWelcomeMail($user, $password));
            return redirect()->back()->with('success', "New login credentials sent to {$buyer->name} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function verifyEmail($id)
    {
        $buyer = Buyer::findOrFail($id);
        $buyer->update(['email_verified_at' => now()]);
        return redirect()->back()->with('success', "Email for {$buyer->name} verified successfully.");
    }

    public function resetPassword($id)
    {
        $buyer = Buyer::findOrFail($id);
        $password = \Illuminate\Support\Str::random(10);
        $buyer->update(['password' => Hash::make($password), 'require_password_change' => true]);

        try {
            \App\Models\Setting::configureMailer();
            Mail::to($buyer->email)->send(new UserPasswordResetMail($user, $password));
            return redirect()->back()->with('success', "Password for {$buyer->name} reset successfully. New credentials sent to user email.");
        } catch (\Exception $e) {
            return redirect()->back()->with('success', "Password for {$buyer->name} reset to: {$password} (Email failed)");
        }
    }

    public function sendCustomEmail(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $buyer = Buyer::findOrFail($id);

        try {
            \App\Models\Setting::configureMailer();
            Mail::to($buyer->email)->send(new GeneralUserMail($user, $request->subject, $request->body));
            return redirect()->back()->with('success', "Email sent to {$buyer->name} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|string',
        ]);

        $ids = json_decode($request->ids);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'No users selected.');
        }

        $buyers = Buyer::whereIn('id', $ids)->get();
        $count = $buyers->count();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $users) {
            foreach ($buyers as $user) {
                switch ($request->action) {
                    case 'activate':
                        $buyer->update(['is_active' => true]);
                        break;
                    case 'suspend':
                        $buyer->update(['is_active' => false]);
                        break;
                    case 'require_password':
                        $buyer->update(['require_password_change' => true]);
                        break;
                    case 'remove_password_req':
                        $buyer->update(['require_password_change' => false]);
                        break;
                    case 'delete':
                        $buyer->delete();
                        break;
                }
            }
        });

        $actionName = str_replace('_', ' ', $request->action);
        return redirect()->back()->with('success', "Bulk action '{$actionName}' completed for {$count} users.");
    }

    public function unlock($id)
    {
        try {
            $buyer = Buyer::findOrFail($id);
            $buyer->update(['locked_until' => null]);
            
            \App\Models\AuthenticationLog::log($user, 'unlock', [
                'unlocked_by' => auth()->id()
            ]);

            return redirect()->back()->with('success', 'User account has been unlocked.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to unlock user.');
        }
    }

    public function authLogs($id)
    {
        $buyer = Buyer::findOrFail($id);
        $logs = \App\Models\AuthenticationLog::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.buyers.auth-logs', compact('buyer', 'logs'));
    }

    public function allAuthLogs()
    {
        $logs = \App\Models\AuthenticationLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.buyers.all-auth-logs', compact('logs'));
    }

    public function restore($id)
    {
        $buyer = Buyer::withTrashed()->findOrFail($id);
        $buyer->restore();
        return redirect()->route('admin.buyers.index')->with('success', "Account for {$buyer->name} has been restored successfully.");
    }

    public function forceDelete($id)
    {
        $buyer = Buyer::withTrashed()->findOrFail($id);
        $name = $buyer->name;
        $buyer->forceDelete();
        return redirect()->route('admin.buyers.index', ['view' => 'trash'])->with('success', "Account for {$name} has been permanently deleted.");
    }
}

