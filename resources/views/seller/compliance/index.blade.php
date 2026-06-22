@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Compliance & Review Status</h1>
            <p class="text-slate-500 text-sm mt-1">Track your verification progress and regulatory reviews.</p>
        </div>
        
        <div class="flex items-center space-x-3">
            @if($profile->seller_tier === 'export')
                <span class="px-3 py-1 bg-purple-100 text-purple-700 font-semibold text-xs rounded-full border border-purple-200">
                    <i data-lucide="globe" class="w-3 h-3 inline-block mr-1"></i> Export Seller
                </span>
            @else
                <span class="px-3 py-1 bg-green-100 text-green-700 font-semibold text-xs rounded-full border border-green-200">
                    <i data-lucide="store" class="w-3 h-3 inline-block mr-1"></i> Local Seller
                </span>
            @endif

            @if($profile->verification_status === 'approved')
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-semibold text-xs rounded-full border border-emerald-200">
                    <i data-lucide="check-circle" class="w-3 h-3 inline-block mr-1"></i> Approved
                </span>
            @elseif($profile->verification_status === 'pending')
                <span class="px-3 py-1 bg-amber-100 text-amber-700 font-semibold text-xs rounded-full border border-amber-200">
                    <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i> Under Review
                </span>
            @elseif($profile->verification_status === 'rejected')
                <span class="px-3 py-1 bg-red-100 text-red-700 font-semibold text-xs rounded-full border border-red-200">
                    <i data-lucide="x-circle" class="w-3 h-3 inline-block mr-1"></i> Action Required
                </span>
            @endif
        </div>
    </div>

    @if($profile->verification_status === 'rejected' && $profile->rejection_reason)
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-5 mb-6">
            <div class="flex items-start">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-500 mr-3 shrink-0"></i>
                <div>
                    <h3 class="text-red-800 font-bold text-sm">Application Needs Attention</h3>
                    <p class="text-red-700 text-sm mt-1">{{ $profile->rejection_reason }}</p>
                    <a href="{{ route('seller.onboarding') }}" class="inline-block mt-3 px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded shadow-sm hover:bg-red-700 transition-colors">
                        Update Information
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Past Reviews & Notes -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i data-lucide="history" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800">Review History</h2>
                </div>
                
                <div class="p-6">
                    @if(empty($history))
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                                <i data-lucide="clipboard" class="w-8 h-8"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-900">No reviews yet</p>
                            <p class="text-xs text-slate-500 mt-1">When an admin reviews your profile, notes will appear here.</p>
                        </div>
                    @else
                        <div class="relative border-l border-slate-200 ml-3 space-y-8">
                            @foreach($history as $review)
                                <div class="relative pl-6">
                                    <div class="absolute -left-[5px] top-1.5 w-2.5 h-2.5 rounded-full border-2 border-white {{ isset($review['action']) && $review['action'] === 'approved' ? 'bg-emerald-500' : (isset($review['action']) && $review['action'] === 'rejected' ? 'bg-red-500' : 'bg-blue-500') }}"></div>
                                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-bold text-slate-700 capitalize">
                                                {{ $review['action'] ?? 'Review Note' }}
                                            </span>
                                            <span class="text-xs text-slate-400">
                                                {{ \Carbon\Carbon::parse($review['date'] ?? now())->format('M d, Y h:i A') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-slate-600">
                                            {{ $review['note'] ?? 'No notes provided.' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Uploaded Documents -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800">Submitted Documents</h2>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @forelse($documents as $doc)
                        <div class="p-4 flex items-start justify-between hover:bg-slate-50 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded bg-slate-100 flex items-center justify-center text-slate-500 shrink-0 mt-0.5">
                                    <i data-lucide="file" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800 capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $doc->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            
                            @if($doc->status === 'approved')
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded uppercase tracking-wider">Verified</span>
                            @elseif($doc->status === 'rejected')
                                <span class="px-2 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded uppercase tracking-wider">Rejected</span>
                            @else
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 text-[10px] font-bold rounded uppercase tracking-wider">Pending</span>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <p class="text-sm text-slate-500">No documents submitted.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
