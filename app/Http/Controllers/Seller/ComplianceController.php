<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceController extends Controller
{
    /**
     * Display the seller's compliance and review status.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        $profile = SellerProfile::where('user_id', $user->id)->firstOrFail();
        // Collect all profiles for the user
        $profiles = [
            'buyer' => $user->buyerProfile,
            'seller' => $profile,
            'logistics' => $user->logisticsProfile,
            'admin' => $user->adminProfile,
        ];
        $profiles = array_filter($profiles);

        $history = [];
        $allDocuments = collect();

        foreach ($profiles as $type => $prof) {
            // Parse regulatory field reviews for this profile
            if (is_array($prof->regulatory_reviews)) {
                $typeLabel = ucfirst($type);
                foreach ($prof->regulatory_reviews as $field => $review) {
                    if (isset($review['status'])) {
                        $history[] = [
                            'action' => $review['status'],
                            'date' => $prof->updated_at,
                            'note' => !empty($review['comment']) 
                                ? "[{$typeLabel}] Field '" . str_replace('_', ' ', $field) . "': " . $review['comment'] 
                                : "[{$typeLabel}] Field '" . str_replace('_', ' ', $field) . "' was " . $review['status'] . ".",
                        ];
                    }
                }
            }

            // Fetch documents for this profile
            $docs = \App\Models\Document::where('owner_type', $type)
                ->where('owner_id', $prof->id)
                ->get();
            
            $allDocuments = $allDocuments->concat($docs);

            // Parse document reviews
            foreach ($docs as $doc) {
                if ($doc->reviewed_at && $doc->status !== 'pending') {
                    $docName = str_replace('_', ' ', $doc->document_type);
                    $history[] = [
                        'action' => $doc->status,
                        'date' => $doc->reviewed_at,
                        'note' => !empty($doc->review_comment) 
                            ? "Document '{$docName}': " . $doc->review_comment 
                            : "Document '{$docName}' was " . $doc->status . ".",
                    ];
                }
            }
        }

        // Sort all documents by latest
        $documents = $allDocuments->sortByDesc('created_at')->values();

        // Fetch overall profile verification logs
        $profileTypeIds = [];
        foreach ($profiles as $type => $prof) {
            $profileTypeIds[$type . '_profile'] = $prof->id;
        }

        $auditLogs = \App\Models\AtexAuditLog::where('action', 'reviewed_kyc_status')
            ->where(function ($query) use ($profileTypeIds) {
                foreach ($profileTypeIds as $auditableType => $id) {
                    $query->orWhere(function ($q) use ($auditableType, $id) {
                        $q->where('auditable_type', $auditableType)
                          ->where('auditable_id', $id);
                    });
                }
            })
            ->get();

        // Used to track "Export Seller" vs "Local Seller" if there are multiple seller approvals
        $sellerApprovalCount = 0;

        foreach ($auditLogs as $log) {
            $newVals = json_decode($log->new_values, true);
            $status = $newVals['verification_status'] ?? 'reviewed';
            
            $typeStr = ucwords(str_replace('_', ' ', $log->auditable_type));

            if ($log->auditable_type === 'seller_profile' && $status === 'approved') {
                $sellerApprovalCount++;
                if ($sellerApprovalCount === 1) {
                    $typeStr = 'Local Seller Profile';
                } else {
                    $typeStr = 'Export Seller Profile';
                }
            }
            
            $history[] = [
                'action' => $status,
                'date' => $log->created_at,
                'note' => "Overall {$typeStr} status was updated to {$status}.",
            ];
        }

        // Sort descending by date

        // Sort descending by date
        usort($history, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return view('seller.compliance.index', compact('user', 'profile', 'documents', 'history'));
    }
}
