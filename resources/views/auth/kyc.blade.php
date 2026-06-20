@extends('layouts.landing')

@section('styles')
<style>
  .kyc-container {
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
  }

  .kyc-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
    width: 100%;
    max-width: 520px;
    padding: 2rem;
  }

  .kyc-card h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
  }

  .kyc-card p {
    color: #64748b;
    font-size: 0.875rem;
  }

  .form-group { margin-bottom: 1.25rem; }

  .form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.375rem;
  }

  .form-input {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: border-color 0.15s;
  }

  .form-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
  }

  textarea.form-input { min-height: 80px; }

  .input-error {
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 0.25rem;
  }

  .btn-submit {
    width: 100%;
    padding: 0.75rem;
    background: #10b981;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.5rem;
  }

  .btn-submit:hover { background: #059669; }

  .btn-logout {
    width: 100%;
    padding: 0.75rem;
    background: white;
    color: #64748b;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.75rem;
  }

  .btn-logout:hover { background: #f8fafc; }

  .alert {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-bottom: 1.25rem;
  }

  .alert-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
  }

  .alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
  }

  .alert-error strong { display: block; margin-bottom: 0.25rem; }

  .pending-state { text-align: center; padding: 1rem; }
  .pending-icon { width: 48px; height: 48px; color: #f59e0b; margin: 0 auto 1rem; }

  .section-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }

  .doc-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.375rem;
  }

  .doc-status .badge {
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
  }

  .doc-status .badge.rejected {
    background: #fef2f2;
    color: #dc2626;
  }

  .doc-comment {
    font-size: 0.8rem;
    color: #dc2626;
    background: #fef2f2;
    padding: 0.375rem 0.625rem;
    border-radius: 6px;
    margin-bottom: 0.375rem;
  }

  .mt-2 { margin-top: 0.5rem; }
  .mb-2 { margin-bottom: 0.5rem; }
  .text-center { text-align: center; }
  .text-sm { font-size: 0.875rem; }
  .text-muted { color: #64748b; }
</style>
@endsection

@section('content')
<div class="kyc-container">
  <div class="kyc-card">
    @php
      $user = Auth::user();
      $isPending = $profile && $profile->verification_status === 'pending';
      $isApproved = $profile && $profile->verification_status === 'approved';
      $isRejected = $profile && $profile->verification_status === 'rejected';
      $isExisting = (bool) $profile;
    @endphp

    @if($isPending)
      <div class="pending-state">
        <i data-lucide="clock" class="pending-icon"></i>
        <h1 style="font-size: 1.25rem;">Under Review</h1>
        <p>Your application is being reviewed. We'll notify you once approved.</p>
      </div>
    @elseif($isApproved)
      <div class="alert alert-success text-center">
        <i data-lucide="check-circle" style="width: 20px; height: 20px; display: block; margin: 0 auto 0.25rem;"></i>
        KYC Approved
      </div>
    @elseif($isRejected)
      <div class="alert alert-error">
        <strong>KYC Rejected</strong>
        @if($profile->rejection_reason)
          <p style="margin-top: 0.25rem;">{{ $profile->rejection_reason }}</p>
        @endif
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
        <div style="margin-bottom: 1rem;">
          <p class="text-sm font-semibold" style="color: #991b1b; margin-bottom: 0.5rem;">Fields needing correction:</p>
          <div style="display: flex; flex-direction: column; gap: 0.375rem;">
            @foreach($rejectedFields as $field => $review)
              <div style="font-size: 0.8rem; padding: 0.375rem 0.625rem; background: #fef2f2; border-radius: 6px; border-left: 3px solid #dc2626;">
                <strong>{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</strong>
                @if(!empty($review['comment']))
                  <span style="display: block; color: #991b1b; margin-top: 0.125rem;">{{ $review['comment'] }}</span>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endif
    @endif

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @unless($isPending)
    <form method="POST" action="{{ route('kyc.onboarding.store') }}" enctype="multipart/form-data">
      @csrf

      @if($user->hasRole('buyer'))
          <h1>Complete Your Profile</h1>
          <p class="mb-2">Please provide your shipping and billing details.</p>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $profile->phone_number ?? '') }}" class="form-input" required>
                @error('phone_number') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-input">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $profile->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $profile->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $profile->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender') <p class="input-error">{{ $message }}</p> @enderror
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Shipping Address *</label>
            <textarea name="shipping_address" class="form-input" required>{{ old('shipping_address', $profile->shipping_address ?? '') }}</textarea>
            @error('shipping_address') <p class="input-error">{{ $message }}</p> @enderror
          </div>

          <div class="form-group">
            <label class="form-label">Billing Address (Optional)</label>
            <textarea name="billing_address" class="form-input">{{ old('billing_address', $profile->billing_address ?? '') }}</textarea>
            @error('billing_address') <p class="input-error">{{ $message }}</p> @enderror
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label">City *</label>
                <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}" class="form-input" required>
                @error('city') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">State *</label>
                <input type="text" name="state" value="{{ old('state', $profile->state ?? '') }}" class="form-input" required>
                @error('state') <p class="input-error">{{ $message }}</p> @enderror
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label">Zip Code *</label>
                <input type="text" name="zip_code" value="{{ old('zip_code', $profile->zip_code ?? '') }}" class="form-input" required>
                @error('zip_code') <p class="input-error">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Country *</label>
                <input type="text" name="country" value="{{ old('country', $profile->country ?? 'Nigeria') }}" class="form-input" required>
                @error('country') <p class="input-error">{{ $message }}</p> @enderror
            </div>
          </div>
      @else
        @unless($profile)
        <h1>Complete Your Profile</h1>
        <p class="mb-2">Please provide your details to get started.</p>

        @if($user->hasRole('admin'))
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-input" required>
            @error('full_name') <p class="input-error">{{ $message }}</p> @enderror
          </div>
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-input" required>
            @error('phone') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @else
          <div class="form-group">
            <label class="form-label">Business/Company Name</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}" class="form-input" required>
            @error('business_name') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endif

        <div class="form-group">
          <label class="form-label">CAC Registration Number</label>
          <input type="text" name="registration_number" value="{{ old('registration_number') }}" class="form-input">
          @error('registration_number') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Tax Identification Number (TIN)</label>
          <input type="text" name="tax_number" value="{{ old('tax_number') }}" class="form-input">
          @error('tax_number') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
          <label class="form-label">BVN</label>
          <input type="text" name="bvn" value="{{ old('bvn') }}" class="form-input" required maxlength="11">
          @error('bvn') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
          <label class="form-label">NIN</label>
          <input type="text" name="nin" value="{{ old('nin') }}" class="form-input" required maxlength="11">
          @error('nin') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Business Address</label>
          <textarea name="address" class="form-input" required>{{ old('address') }}</textarea>
          @error('address') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <p class="section-title">Bank Details</p>

        <div class="form-group">
          <label class="form-label">Bank Name</label>
          <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-input" required>
          @error('bank_name') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Account Number</label>
          <input type="text" name="account_number" value="{{ old('account_number') }}" class="form-input" required>
          @error('account_number') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
          <label class="form-label">Account Name</label>
          <input type="text" name="account_name" value="{{ old('account_name') }}" class="form-input" required>
          @error('account_name') <p class="input-error">{{ $message }}</p> @enderror
        </div>

        @if($user->hasRole('seller') || $user->hasRole('buyer'))
          <div class="form-group">
            <label class="form-label">Monthly Trade Volume Capacity</label>
            <input type="text" name="trade_capacity" value="{{ old('trade_capacity') }}" class="form-input">
            @error('trade_capacity') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endif

        @if($user->hasRole('logistics'))
          <div class="form-group">
            <label class="form-label">Fleet Size</label>
            <input type="text" name="fleet_size" value="{{ old('fleet_size') }}" class="form-input">
            @error('fleet_size') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endif

        @if($user->hasRole('admin'))
          <div class="form-group">
            <label class="form-label">Staff ID Number</label>
            <input type="text" name="identification_number" value="{{ old('identification_number') }}" class="form-input">
            @error('identification_number') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endif

        <p class="section-title">Documents</p>
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
          <p class="section-title">Rejected Fields</p>
          <p class="text-sm text-muted mb-2">Correct the fields below and resubmit.</p>
          @foreach($rejectedFields as $field => $review)
            <div class="form-group">
              <label class="form-label">{{ $fieldLabels[$field] }}</label>
              @if(!empty($review['comment']))
                <p class="doc-comment" style="margin-bottom: 0.375rem;">{{ $review['comment'] }}</p>
              @endif
              @if(($fieldInputs[$field] ?? 'text') === 'textarea')
                <textarea name="{{ $field }}" class="form-input">{{ old($field, $profile->$field ?? '') }}</textarea>
              @else
                <input type="text" name="{{ $field }}" value="{{ old($field, $profile->$field ?? '') }}" class="form-input" {{ in_array($field, ['bvn','nin']) ? 'maxlength="11"' : '' }}>
              @endif
              @error($field) <p class="input-error">{{ $message }}</p> @enderror
            </div>
          @endforeach
        @endif

        <p class="section-title">Rejected Documents</p>
        <p class="text-sm text-muted mb-2">Re-upload the rejected documents below.</p>

        @foreach(['cac_document' => 'CAC Certificate', 'valid_id' => 'Valid ID', 'proof_of_address' => 'Proof of Address'] as $key => $label)
          @if(($doc = $documents->get($key)) && $doc->status === 'rejected')
            <div class="form-group">
              <label class="form-label">{{ $label }}</label>
              <div class="doc-status">
                <span class="badge rejected">Rejected</span>
              </div>
              @if($doc->review_comment)
                <p class="doc-comment">{{ $doc->review_comment }}</p>
              @endif
              <input type="file" name="{{ $key }}" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
              @error($key) <p class="input-error">{{ $message }}</p> @enderror
            </div>
          @endif
        @endforeach

        @if($user->hasRole('seller'))
          @if(($doc = $documents->get('nepc_certificate')) && $doc->status === 'rejected')
            <div class="form-group">
              <label class="form-label">NEPC Certificate</label>
              <div class="doc-status"><span class="badge rejected">Rejected</span></div>
              @if($doc->review_comment) <p class="doc-comment">{{ $doc->review_comment }}</p> @endif
              <input type="file" name="nepc_certificate" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
              @error('nepc_certificate') <p class="input-error">{{ $message }}</p> @enderror
            </div>
          @endif
        @endif

        @if($user->hasRole('logistics'))
          @if(($doc = $documents->get('git_insurance')) && $doc->status === 'rejected')
            <div class="form-group">
              <label class="form-label">Goods in Transit Insurance</label>
              <div class="doc-status"><span class="badge rejected">Rejected</span></div>
              @if($doc->review_comment) <p class="doc-comment">{{ $doc->review_comment }}</p> @endif
              <input type="file" name="git_insurance" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
              @error('git_insurance') <p class="input-error">{{ $message }}</p> @enderror
            </div>
          @endif
        @endif
      @else
        @foreach(['cac_document' => 'CAC Certificate', 'valid_id' => 'Valid ID', 'proof_of_address' => 'Proof of Address'] as $key => $label)
          <div class="form-group">
            <label class="form-label">{{ $label }}</label>
            <input type="file" name="{{ $key }}" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
            @error($key) <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endforeach

        @if($user->hasRole('seller'))
          <div class="form-group">
            <label class="form-label">NEPC Certificate</label>
            <input type="file" name="nepc_certificate" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
            @error('nepc_certificate') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endif

        @if($user->hasRole('logistics'))
          <div class="form-group">
            <label class="form-label">Goods in Transit Insurance</label>
            <input type="file" name="git_insurance" class="form-input" accept=".pdf,.jpg,.jpeg,.png" required>
            @error('git_insurance') <p class="input-error">{{ $message }}</p> @enderror
          </div>
        @endif
        @endif
      @endif

      <button type="submit" class="btn-submit">{{ $isExisting ? 'Resubmit' : 'Submit' }}</button>
    </form>
    @endunless

    @if(!$isExisting)
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">Logout</button>
      </form>
    @endif
  </div>
</div>
@endsection