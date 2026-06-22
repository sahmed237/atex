<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\SellerProfile;
use App\Models\AtexAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $documents = Document::with('reviewer')->latest()->get();
            return view('seller.documents.admin', compact('documents'));
        }

        if ($user->hasRole('seller')) {
            $profile = SellerProfile::where('user_id', $user->id)->first();
            $documents = Document::where('owner_type', 'seller')
                ->where('owner_id', $profile->id ?? 0)
                ->with('reviewer')
                ->latest()
                ->get();
            return view('seller.documents.seller', compact('documents', 'profile'));
        }

        return redirect()->route('admin.dashboard');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'document_type' => 'required|string|max:100',
            'document_file' => 'required|file|max:5120',
            'expiry_date' => 'nullable|date',
        ]);

        $profile = SellerProfile::where('user_id', $user->id)->first();
        $ownerType = 'seller';
        $ownerId = $profile->id ?? 0;

        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            $ownerType = $request->owner_type ?: 'seller';
            $ownerId = $request->owner_id ?: 1;
        }

        $filePath = null;
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $file->move(public_path('storage/uploads/documents'), $fileName);
            $filePath = 'storage/uploads/documents/' . $fileName;
        }

        $doc = Document::create([
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'document_type' => $request->document_type,
            'title' => $request->title,
            'path' => $filePath,
            'status' => 'pending',
            'expiry_date' => $request->expiry_date,
        ]);

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'uploaded_document',
            'auditable_type' => 'document',
            'auditable_id' => $doc->id,
            'new_values' => json_encode(['title' => $request->title]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully and is pending verification.');
    }

    public function review(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comment' => 'nullable|string',
        ]);

        $document = Document::findOrFail($id);
        $oldStatus = $document->status;
        $document->update([
            'status' => $request->status,
            'review_comment' => $request->comment,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        AtexAuditLog::create([
            'actor_id' => $user->id,
            'action' => 'reviewed_document',
            'auditable_type' => 'document',
            'auditable_id' => $document->id,
            'old_values' => json_encode(['status' => $oldStatus]),
            'new_values' => json_encode(['status' => $request->status, 'comment' => $request->comment]),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.documents.index')->with('success', 'Document status updated to ' . $request->status . '.');
    }
}

