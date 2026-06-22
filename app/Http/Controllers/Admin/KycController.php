<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use App\Models\BuyerProfile;
use App\Models\LogisticsProfile;
use App\Models\AdminProfile;
use App\Models\Document;
use App\Models\AtexAuditLog;
use App\Events\KycApproved;
use App\Events\KycRejected;
use App\Notifications\KycApprovedNotification;
use App\Notifications\KycRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    private function getProfileModel(string $type)
    {
        return match ($type) {
            'seller' => new SellerProfile,
            'buyer' => new BuyerProfile,
            'logistics' => new LogisticsProfile,
            'admin' => new AdminProfile,
            default => null,
        };
    }

    private function getProfileClass(string $type): ?string
    {
        return match ($type) {
            'seller' => SellerProfile::class,
            'buyer' => BuyerProfile::class,
            'logistics' => LogisticsProfile::class,
            'admin' => AdminProfile::class,
            default => null,
        };
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        // Dropdown filter: all | export | buyer | logistics | admin
        $filter = $request->query('type', 'all');
        $filters = [
            'all' => 'All KYC',
            'seller' => 'Seller KYC',
            'export' => 'Exporter KYC',
            'buyer' => 'Buyer KYC',
            'logistics' => 'Logistics KYC',
            'admin' => 'Admin KYC',
        ];
        if (!array_key_exists($filter, $filters)) {
            $filter = 'all';
        }

        $profiles = collect();

        $profileTypes = [
            'seller' => SellerProfile::class,
            'buyer' => BuyerProfile::class,
            'logistics' => LogisticsProfile::class,
            'admin' => AdminProfile::class,
        ];

        foreach ($profileTypes as $type => $class) {
            $records = $class::with('user')->get()->map(function ($profile) use ($type) {
                $sellerTier = $type === 'seller' ? ($profile->seller_tier ?? 'local') : null;

                $org = match ($type) {
                    'seller' => $profile->business_name,
                    'buyer' => $profile->company_name ?: ($profile->user->name ?? 'Buyer Account'),
                    'logistics' => $profile->company_name,
                    'admin' => $profile->full_name ?: 'Admin',
                    default => 'Unknown',
                };

                $category = match ($type) {
                    'seller' => $profile->business_category ?: $profile->business_type,
                    'buyer' => 'Buyer',
                    'logistics' => 'logistics',
                    'admin' => 'admin',
                    default => null,
                };

                $location = match ($type) {
                    'seller' => $profile->state ?: $profile->lga,
                    'buyer' => $profile->country,
                    'logistics' => $profile->coverage_regions,
                    'admin' => $profile->address,
                    default => null,
                };

                $labels = [
                    'seller' => $sellerTier === 'export' ? 'Export Seller' : 'Local Seller',
                    'buyer' => 'Buyer',
                    'logistics' => 'Logistics',
                    'admin' => 'Admin',
                ];

                return [
                    'id' => $profile->id,
                    'profile_type' => $type,
                    'seller_tier' => $sellerTier,
                    'profile_type_label' => $labels[$type] ?? ucfirst($type),
                    'organization' => $org,
                    'name' => $profile->user->name ?? '',
                    'email' => $profile->user->email ?? '',
                    'account_status' => $profile->user->status ?? 'pending',
                    'verification_status' => $profile->verification_status,
                    'profile_category' => $category,
                    'location' => $location,
                    'bvn' => $profile->bvn ?? 'N/A',
                    'nin' => $profile->nin ?? 'N/A',
                    'rc_number' => $profile->registration_number ?? 'N/A',
                    'documents' => Document::where('owner_type', $type)->where('owner_id', $profile->id)->get(),
                    'documents_count' => Document::where('owner_type', $type)->where('owner_id', $profile->id)->count(),
                ];
            });

            $profiles = $profiles->concat($records);
        }

        // Apply dropdown filter
        $profiles = $profiles->filter(function ($item) use ($filter) {
            return match ($filter) {
                'seller' => $item['profile_type'] === 'seller' && $item['seller_tier'] !== 'export',
                'export' => $item['profile_type'] === 'seller' && $item['seller_tier'] === 'export',
                'buyer' => $item['profile_type'] === 'buyer',
                'logistics' => $item['profile_type'] === 'logistics',
                'admin' => $item['profile_type'] === 'admin',
                default => true,
            };
        });

        $profiles = $profiles->sortByDesc(function ($item) {
            return in_array($item['verification_status'], ['pending', 'submitted']) ? 1 : 0;
        })->values()->toArray();

        return view('admin.kyc.index', compact('profiles', 'filters', 'filter'));
    }

    public function review(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'profile_type' => 'required|in:seller,buyer,logistics,admin',
            'profile_id' => 'required|integer',
            'status' => 'required|in:approved,rejected,pending',
            'reason' => 'nullable|string|max:1000',
        ]);

        $profileType = $request->profile_type;
        $profileId = $request->profile_id;
        $status = $request->status;

        $class = $this->getProfileClass($profileType);
        if (!$class) {
            abort(404);
        }

        $profile = $class::findOrFail($profileId);
        $oldStatus = $profile->verification_status;

        $updateData = ['verification_status' => $status];

        if ($status === 'approved') {
            $updateData['approved_at'] = now();
            $updateData['rejection_reason'] = null;
            if ($profileType === 'seller') {
                $updateData['seller_program_status'] = 'approved';
            }
        } elseif ($status === 'rejected') {
            $updateData['rejection_reason'] = $request->reason;
        }

        $profile->update($updateData);


        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'reviewed_kyc_status',
            'auditable_type' => $profileType . '_profile',
            'auditable_id' => $profileId,
            'old_values' => json_encode(['verification_status' => $oldStatus]),
            'new_values' => json_encode(['verification_status' => $status]),
            'ip_address' => $request->ip(),
        ]);

        // Fire events and send notifications
        $profileUser = $profile->user;
        if ($profileUser) {
            if ($status === 'approved') {
                if ($profileType === 'seller') {
                    $profileUser->assignRole('seller');
                } elseif ($profileType === 'logistics') {
                    $profileUser->assignRole('logistics');
                }
                event(new KycApproved($profileUser, $profileType, $profile, $user->name));
                $profileUser->notify(new KycApprovedNotification($profileUser, $profileType));
            } elseif ($status === 'rejected') {
                $reason = $request->reason;
                event(new KycRejected($profileUser, $profileType, $profile, $reason, $user->name));
                $profileUser->notify(new KycRejectedNotification($profileUser, $profileType, $reason));
            }
        }

        $returnTo = $request->return_to ?: 'kyc';
        if ($returnTo === 'kyc') {
            return redirect()->route('admin.kyc.index')->with('success', 'KYC verification status updated.');
        }

        return redirect()->back()->with('success', 'KYC verification status updated.');
    }

    public function show($type, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $class = $this->getProfileClass($type);
        if (!$class) {
            abort(404);
        }

        $profile = $class::with('user')->findOrFail($id);
        $documents = Document::where('owner_type', $type)->where('owner_id', $profile->id)->get();

        return view('admin.kyc.show', compact('profile', 'type', 'documents'));
    }

    public function reviewDocument(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:1000',
        ]);

        $document = Document::findOrFail($id);
        $document->update([
            'status' => $request->status,
            'reviewed_by' => $user->id,
            'review_comment' => $request->comment,
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Document review updated.');
    }

    public function reviewAllDocuments(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'profile_type' => 'required|in:seller,buyer,logistics,admin',
            'profile_id' => 'required|integer',
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:1000',
        ]);

        $selections = $request->documents ?? [];

        if ($request->status === 'approved') {
            Document::where('owner_type', $request->profile_type)
                ->where('owner_id', $request->profile_id)
                ->update([
                    'status' => 'approved',
                    'reviewed_by' => $user->id,
                    'reviewed_at' => now(),
                ]);
            return redirect()->back()->with('success', 'All documents approved.');
        }

        $allDocs = Document::where('owner_type', $request->profile_type)
            ->where('owner_id', $request->profile_id)
            ->get();

        foreach ($allDocs as $doc) {
            $selected = $selections[$doc->id] ?? null;
            if ($selected === 'approved') {
                $doc->update([
                    'status' => 'approved',
                    'reviewed_by' => $user->id,
                    'review_comment' => null,
                    'reviewed_at' => now(),
                ]);
            } else {
                $doc->update([
                    'status' => 'rejected',
                    'reviewed_by' => $user->id,
                    'review_comment' => $request->comment,
                    'reviewed_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Document review saved.');
    }

    public function reviewRegulatory(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'profile_type' => 'required|in:seller,buyer,logistics,admin',
            'profile_id' => 'required|integer',
            'fields' => 'nullable|array',
            'fields.*.status' => 'nullable|in:approved,rejected',
            'fields.*.comment' => 'nullable|string|max:1000',
        ]);

        $class = $this->getProfileClass($request->profile_type);
        if (!$class) {
            abort(404);
        }

        $profile = $class::findOrFail($request->profile_id);

        $existing = $profile->regulatory_reviews ?? [];
        $incoming = $request->fields ?? [];
        // Merge: only update fields that were submitted, keep existing for others
        foreach ($incoming as $field => $review) {
            $existing[$field] = $review;
        }
        $profile->regulatory_reviews = $existing;
        $profile->save();

        return redirect()->back()->with('success', 'Regulatory field reviews saved.');
    }
}
