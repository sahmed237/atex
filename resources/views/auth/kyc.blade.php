@extends('layouts.landing')

@section('styles')
<style>
.kyc-page { padding: 40px 24px 80px; max-width: 800px; margin: 0 auto; }
.kyc-page h1 { font-size: 1.5rem; font-weight: 700; color: #0f172a; }
.kyc-page .sub { color: #64748b; font-size: .9rem; margin-bottom: 32px; }

.kyc-steps { display: flex; gap: 0; margin-bottom: 40px; counter-reset: step; }
.kyc-step { flex: 1; text-align: center; position: relative; counter-increment: step; }
.kyc-step::before { content: counter(step); display: block; width: 36px; height: 36px; border-radius: 50%; background: #e2e8f0; color: #64748b; font-weight: 700; font-size: .9rem; line-height: 36px; margin: 0 auto 8px; transition: all .3s; }
.kyc-step.active::before { background: #febd69; color: #131921; box-shadow: 0 0 0 4px rgba(254,189,105,.25); }
.kyc-step.done::before { background: #16a34a; color: #fff; content: '✓'; }
.kyc-step::after { content: ''; position: absolute; top: 18px; left: calc(50% + 22px); right: calc(-50% + 22px); height: 3px; background: #e2e8f0; z-index: -1; }
.kyc-step:last-child::after { display: none; }
.kyc-step.done::after { background: #16a34a; }
.kyc-step .label { font-size: .78rem; color: #64748b; font-weight: 500; }
.kyc-step.active .label { color: #0f172a; font-weight: 600; }
.kyc-step.done .label { color: #16a34a; }

.status-banner { padding: 16px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; margin-bottom: 24px; font-size: .9rem; }
.status-banner.verified { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.status-banner.pending { background: #fefce8; border: 1px solid #fde68a; color: #92400e; }
.status-banner.rejected { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.status-banner .icon { font-size: 1.5rem; flex-shrink: 0; }

.kyc-section { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 32px; margin-bottom: 24px; }
.kyc-section h2 { font-size: 1.1rem; font-weight: 700; margin-bottom: 4px; color: #0f172a; }
.kyc-section .desc { font-size: .85rem; color: #64748b; margin-bottom: 24px; }
.kyc-section .section-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; color: #0f172a; }

.form-row-g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { margin-bottom: 18px; }
.form-group label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: 6px; color: #0f172a; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: .9rem; outline: none; transition: border-color .25s ease; background: #fff; font-family: inherit; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
.form-group textarea { resize: vertical; min-height: 80px; }
.form-group input[type="file"] { padding: 8px; }
.input-error { color: #ef4444; font-size: .8rem; margin-top: 4px; }

.doc-status { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
.doc-status .badge { padding: 2px 10px; border-radius: 50px; font-size: .7rem; font-weight: 600; }
.doc-status .badge.rejected { background: #fef2f2; color: #dc2626; }
.doc-comment { font-size: .8rem; color: #dc2626; background: #fef2f2; padding: 6px 10px; border-radius: 6px; margin-bottom: 6px; }

.rejected-field { font-size: .8rem; padding: 6px 10px; background: #fef2f2; border-radius: 6px; border-left: 3px solid #dc2626; margin-bottom: 6px; }
.rejected-field strong { display: block; color: #991b1b; }
.rejected-field span { display: block; color: #991b1b; margin-top: 2px; }

.kyc-submit { width: 100%; padding: 14px; background: #febd69; border: none; border-radius: 8px; font-size: 1rem; font-weight: 700; color: #131921; cursor: pointer; transition: background .25s ease; }
.kyc-submit:hover { background: #f3a847; }
.kyc-submit:disabled { opacity: .5; cursor: not-allowed; }

.kyc-logout { display: block; text-align: center; margin-top: 24px; font-size: .9rem; color: #64748b; }
.kyc-logout a { color: #2563eb; font-weight: 600; }
.kyc-logout a:hover { text-decoration: underline; }

.pending-state { text-align: center; padding: 32px 20px; }
.pending-icon { width: 48px; height: 48px; margin: 0 auto 16px; }

.toast { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); background: #131921; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: .9rem; opacity: 0; transition: all .3s; z-index: 999; pointer-events: none; }
.toast.visible { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endsection

@section('content')
<div class="kyc-page">
    <h1>KYC Verification</h1>
    <p class="sub">Verify your identity and business to unlock full platform features</p>

    @php
      $user = Auth::user();
      $isPending = $profile && $profile->verification_status === 'pending';
      $isApproved = $profile && $profile->verification_status === 'approved';
      $isRejected = $profile && $profile->verification_status === 'rejected';
      $isExisting = (bool) $profile;
      $sellerTier = $profile->seller_tier ?? null;
      $isExporter = $user->hasRole('seller') && $sellerTier === 'export';
    @endphp

    @if($isPending)
        <div class="status-banner pending">
            <span class="icon">⏳</span>
            <span>Your application is Under Review. We'll notify you once approved.</span>
        </div>
    @elseif($isApproved)
        <div class="status-banner verified">
            <span class="icon">✅</span>
            <span>KYC Approved — you have full platform access.</span>
        </div>
    @elseif($isRejected)
        <div class="status-banner rejected">
            <span class="icon">❌</span>
            <span>
                <strong>KYC Rejected</strong>
                @if($profile->rejection_reason)
                    — {{ $profile->rejection_reason }}
                @endif
            </span>
        </div>

        @php
          $reviews = $profile->regulatory_reviews ?? [];
          $rejectedFields = [];
          $fieldLabels = [
            'registration_number' => 'CAC Registration',
            'tax_number' => 'Tax Identification (TIN)',
            'bvn' => 'Bank Verification (BVN)',
            'nin' => 'National Identity (NIN)',
            'bank_name' => 'Bank Name',
            'account_number' => 'Account Number',
            'account_name' => 'Account Name',
          ];
          foreach ($reviews as $field => $review) {
            if (isset($review['status']) && $review['status'] === 'rejected') {
              $rejectedFields[$field] = $review;
            }
          }
        @endphp

        @if(!empty($rejectedFields))
        <div class="kyc-section">
            <h2>Fields Needing Correction</h2>
            <p class="desc">Please update the following fields and resubmit.</p>
            @foreach($rejectedFields as $field => $review)
                <div class="rejected-field">
                    <strong>{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</strong>
                    @if(!empty($review['comment']))
                        <span>{{ $review['comment'] }}</span>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    @else
        <div class="status-banner" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b;">
            <span class="icon">📋</span>
            <span>Complete your KYC verification to unlock higher trade limits and features.</span>
        </div>
    @endif

    @if(session('success'))
    <div class="status-banner verified" style="margin-bottom: 24px;">
        <span class="icon">✅</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Steps Indicator -->
    @if(!$isPending)
    <div class="kyc-steps">
        <div class="kyc-step {{ $isApproved || $isExisting ? 'done' : 'active' }}"><div class="label">Profile</div></div>
        <div class="kyc-step {{ $isApproved ? 'done' : ($isExisting ? 'active' : '') }}"><div class="label">Details</div></div>
        <div class="kyc-step {{ $isApproved ? 'done' : '' }}"><div class="label">Documents</div></div>
    </div>
    @endif

    @unless($isPending)
    <form method="POST" action="{{ route('kyc.onboarding.store') }}" enctype="multipart/form-data">
        @csrf

        @if($user->hasRole('buyer') && !$user->hasRole('seller') && !$user->hasRole('logistics') && !$user->hasRole('admin'))
            <div class="kyc-section">
                <h2>Profile Information</h2>
                <p class="desc">Please provide your shipping and billing details.</p>

                <div class="form-row-g2">
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $profile->phone_number ?? '') }}" required>
                        @error('phone_number') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $profile->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Shipping Address *</label>
                    <textarea name="shipping_address" required>{{ old('shipping_address', $profile->shipping_address ?? '') }}</textarea>
                    @error('shipping_address') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label>Billing Address (Optional)</label>
                    <textarea name="billing_address">{{ old('billing_address', $profile->billing_address ?? '') }}</textarea>
                    @error('billing_address') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-row-g2">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}" required>
                        @error('city') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>State *</label>
                        <input type="text" name="state" value="{{ old('state', $profile->state ?? '') }}" required>
                        @error('state') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-row-g2">
                    <div class="form-group">
                        <label>Zip Code *</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code', $profile->zip_code ?? '') }}" required>
                        @error('zip_code') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Country *</label>
                        <input type="text" name="country" value="{{ old('country', $profile->country ?? 'Nigeria') }}" required>
                        @error('country') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @else
            @unless($profile)
            <div class="kyc-section">
                <h2>Business Information</h2>
                <p class="desc">Provide your business and identity details for trade verification.</p>

                @if($user->hasRole('admin'))
                    <div class="form-row-g2">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required>
                            @error('phone') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @else
                    <div class="form-group">
                        <label>Business/Company Name</label>
                        <input type="text" name="business_name" value="{{ old('business_name') }}" required>
                        @error('business_name') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="form-row-g2">
                    <div class="form-group">
                        <label>CAC Registration Number</label>
                        <input type="text" name="registration_number" value="{{ old('registration_number') }}">
                        @error('registration_number') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Tax Identification Number (TIN)</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number') }}">
                        @error('tax_number') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-row-g2">
                    <div class="form-group">
                        <label>BVN</label>
                        <input type="text" name="bvn" value="{{ old('bvn') }}" required maxlength="11">
                        @error('bvn') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>NIN</label>
                        <input type="text" name="nin" value="{{ old('nin') }}" required maxlength="11">
                        @error('nin') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Business Address</label>
                    <textarea name="address" required>{{ old('address') }}</textarea>
                    @error('address') <p class="input-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="kyc-section">
                <h2>Bank Details</h2>
                <p class="desc">Provide your bank account information for trade payments.</p>

                <div class="form-row-g2">
                    <div class="form-group">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" required>
                        @error('bank_name') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" required>
                        @error('account_number') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Account Name</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" required>
                    @error('account_name') <p class="input-error">{{ $message }}</p> @enderror
                </div>

                @if($isExporter)
                <div class="form-row-g2">
                    <div class="form-group">
                        <label>Monthly Trade Volume Capacity</label>
                        <input type="text" name="trade_capacity" value="{{ old('trade_capacity', $profile->trade_capacity ?? '') }}" placeholder="e.g. 500kg monthly">
                        @error('trade_capacity') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label>Target Export Markets</label>
                        <input type="text" name="export_markets" value="{{ old('export_markets', $profile->export_markets ?? '') }}" placeholder="e.g. UK, US, EU">
                        @error('export_markets') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                @if($user->hasRole('logistics'))
                <div class="form-group">
                    <label>Fleet Size</label>
                    <input type="text" name="fleet_size" value="{{ old('fleet_size') }}">
                    @error('fleet_size') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                @endif

                @if($user->hasRole('admin'))
                <div class="form-group">
                    <label>Staff ID Number</label>
                    <input type="text" name="identification_number" value="{{ old('identification_number') }}">
                    @error('identification_number') <p class="input-error">{{ $message }}</p> @enderror
                </div>
                @endif
            </div>
            @endunless

            @php
              $documents = $profile
                ? \App\Models\Document::where('owner_type', $user->hasRole('admin') ? 'admin' : ($user->hasRole('seller') ? 'seller' : ($user->hasRole('buyer') ? 'buyer' : 'logistics')))->where('owner_id', $profile->id)->get()->keyBy('document_type')
                : collect();
            @endphp

            @if($isExisting)
                @php
                  $reviews = $profile->regulatory_reviews ?? [];
                  $rejectedFields = [];
                  $fieldLabels = [
                    'registration_number' => 'CAC Registration Number',
                    'tax_number' => 'Tax Identification Number (TIN)',
                    'bvn' => 'BVN',
                    'nin' => 'NIN',
                    'address' => 'Business Address',
                    'bank_name' => 'Bank Name',
                    'account_number' => 'Account Number',
                    'account_name' => 'Account Name',
                  ];
                  $fieldInputs = [
                    'registration_number' => 'text',
                    'tax_number' => 'text',
                    'bvn' => 'text',
                    'nin' => 'text',
                    'address' => 'textarea',
                    'bank_name' => 'text',
                    'account_number' => 'text',
                    'account_name' => 'text',
                  ];
                  foreach ($reviews as $field => $review) {
                    if (isset($review['status']) && $review['status'] === 'rejected' && array_key_exists($field, $fieldLabels)) {
                      $rejectedFields[$field] = $review;
                    }
                  }
                @endphp

                @if(!empty($rejectedFields))
                <div class="kyc-section">
                    <h2>Correct Rejected Fields</h2>
                    <p class="desc">Update the fields below and resubmit.</p>
                    @foreach($rejectedFields as $field => $review)
                    <div class="form-group">
                        <label>{{ $fieldLabels[$field] }}</label>
                        @if(!empty($review['comment']))
                            <p class="doc-comment">{{ $review['comment'] }}</p>
                        @endif
                        @if(($fieldInputs[$field] ?? 'text') === 'textarea')
                            <textarea name="{{ $field }}">{{ old($field, $profile->$field ?? '') }}</textarea>
                        @else
                            <input type="text" name="{{ $field }}" value="{{ old($field, $profile->$field ?? '') }}" {{ in_array($field, ['bvn','nin']) ? 'maxlength="11"' : '' }}>
                        @endif
                        @error($field) <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="kyc-section">
                    <h2>Rejected Documents</h2>
                    <p class="desc">Re-upload the rejected documents below.</p>

                    @foreach(['cac_document' => 'CAC Certificate', 'valid_id' => 'Valid ID', 'proof_of_address' => 'Proof of Address'] as $key => $label)
                      @if(($doc = $documents->get($key)) && $doc->status === 'rejected')
                        <div class="form-group">
                            <label>{{ $label }}</label>
                            <div class="doc-status">
                                <span class="badge rejected">Rejected</span>
                            </div>
                            @if($doc->review_comment)
                                <p class="doc-comment">{{ $doc->review_comment }}</p>
                            @endif
                            <input type="file" name="{{ $key }}" accept=".pdf,.jpg,.jpeg,.png" required>
                            @error($key) <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                      @endif
                    @endforeach

                    @if($isExporter)
                      @if(($doc = $documents->get('nepc_certificate')) && $doc->status === 'rejected')
                        <div class="form-group">
                            <label>NEPC Export Certificate</label>
                            <div class="doc-status"><span class="badge rejected">Rejected</span></div>
                            @if($doc->review_comment) <p class="doc-comment">{{ $doc->review_comment }}</p> @endif
                            <input type="file" name="nepc_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                            @error('nepc_certificate') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                      @endif
                    @endif

                    @if($user->hasRole('logistics'))
                      @if(($doc = $documents->get('git_insurance')) && $doc->status === 'rejected')
                        <div class="form-group">
                            <label>Goods in Transit Insurance</label>
                            <div class="doc-status"><span class="badge rejected">Rejected</span></div>
                            @if($doc->review_comment) <p class="doc-comment">{{ $doc->review_comment }}</p> @endif
                            <input type="file" name="git_insurance" accept=".pdf,.jpg,.jpeg,.png" required>
                            @error('git_insurance') <p class="input-error">{{ $message }}</p> @enderror
                        </div>
                      @endif
                    @endif
                </div>
            @else
                <div class="kyc-section">
                    <h2>Document Upload</h2>
                    <p class="desc">Upload clear copies of the required documents.</p>

                    <div class="form-group">
                        <label>Valid ID</label>
                        <input type="file" name="valid_id" accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('valid_id') <p class="input-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label>Proof of Address</label>
                        <input type="file" name="proof_of_address" accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('proof_of_address') <p class="input-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label>CAC Certificate {{ $isExporter ? '' : '(optional)' }}</label>
                        <input type="file" name="cac_document" accept=".pdf,.jpg,.jpeg,.png" {{ $isExporter ? 'required' : '' }}>
                        @error('cac_document') <p class="input-error">{{ $message }}</p> @enderror
                    </div>

                    @if($isExporter)
                    <div class="form-group">
                        <label>NEPC Export Certificate</label>
                        <input type="file" name="nepc_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('nepc_certificate') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    @if($user->hasRole('logistics'))
                    <div class="form-group">
                        <label>Goods in Transit Insurance</label>
                        <input type="file" name="git_insurance" accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('git_insurance') <p class="input-error">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>
            @endif
        @endif

        <button type="submit" class="kyc-submit">{{ $isExisting ? 'Resubmit' : 'Submit' }}</button>
    </form>
    @endunless

    @if(!$isExisting)
        <div class="kyc-logout">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">← Back to Sign Out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    @endif
</div>
@endSection
