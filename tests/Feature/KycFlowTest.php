<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BuyerProfile;
use App\Models\SellerProfile;
use App\Models\Document;
use App\Models\Buyer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KycFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin', 'seller', 'buyer', 'logistics'] as $role) {
            \Spatie\Permission\Models\Role::create(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_buyer_kyc_is_auto_approved(): void
    {
        $user = User::factory()->create();
        $user->assignRole('buyer');

        $response = $this
            ->actingAs($user)
            ->post(route('kyc.onboarding.store'), [
                'phone_number' => '08012345678',
                'gender' => 'Male',
                'shipping_address' => '123 Main St',
                'billing_address' => '456 Elm St',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'zip_code' => '100001',
                'country' => 'Nigeria',
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success', 'Profile completed successfully.');

        $profile = BuyerProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('approved', $profile->verification_status);
        $this->assertNotNull($profile->approved_at);
        $this->assertEquals('08012345678', $profile->phone_number);
        $this->assertEquals('Nigeria', $profile->country);
        $this->assertEquals('Lagos', $profile->state);

        $user->refresh();
        $this->assertEquals('approved', $user->kyc_verification_status);
        $this->assertNotNull($user->kyc_approved_at);
    }

    public function test_seller_kyc_is_pending_after_submission(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('seller');

        $response = $this
            ->actingAs($user)
            ->post(route('kyc.onboarding.store'), [
                'business_name' => 'Test Exports Ltd',
                'registration_number' => 'RC123456',
                'tax_number' => 'TIN12345',
                'bvn' => '12345678901',
                'nin' => '98765432101',
                'country' => 'Nigeria',
                'state' => 'Lagos',
                'address' => '10 Export Road, Lagos',
                'bank_name' => 'GTBank',
                'account_number' => '0123456789',
                'account_name' => 'Test Exports Ltd',
                'trade_capacity' => '10,000 units/month',
                'cac_document' => UploadedFile::fake()->create('cac.pdf', 100),
                'valid_id' => UploadedFile::fake()->create('id.pdf', 100),
                'proof_of_address' => UploadedFile::fake()->create('address.pdf', 100),
                'nepc_certificate' => UploadedFile::fake()->create('nepc.pdf', 100),
            ]);

        $response->assertRedirect(route('kyc.onboarding'));
        $response->assertSessionHas('success');

        $profile = SellerProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('pending', $profile->verification_status);
        $this->assertEquals('Test Exports Ltd', $profile->business_name);
        $this->assertEquals('Nigeria', $profile->country);
        $this->assertEquals('Lagos', $profile->state);

        $user->refresh();
        $this->assertEquals('pending', $user->kyc_verification_status);
        $this->assertNotNull($user->kyc_submitted_at);

        $documents = Document::where('owner_type', 'seller')
            ->where('owner_id', $profile->id)
            ->get();
        $this->assertCount(4, $documents);
        $this->assertEquals('pending', $documents->first()->status);
    }

    public function test_buyer_redirected_when_already_approved(): void
    {
        $user = User::factory()->create();
        $user->assignRole('buyer');

        BuyerProfile::create([
            'user_id' => $user->id,
            'verification_status' => 'approved',
            'approved_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('kyc.onboarding'));

        $response->assertRedirect(route('dashboard'));
    }

    public function test_buyer_form_loads_with_pending_status(): void
    {
        $user = User::factory()->create();
        $user->assignRole('buyer');

        BuyerProfile::create([
            'user_id' => $user->id,
            'verification_status' => 'pending',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('kyc.onboarding'));

        $response->assertOk();
        $response->assertSee('Under Review');
    }

    public function test_seller_onboarding_creates_profile_and_assigns_role(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('seller.onboarding.store'), [
                'business_name' => 'New Seller Co',
                'business_category' => 'Agriculture',
                'business_type' => 'SME',
                'country' => 'Nigeria',
                'state' => 'Abuja',
                'lga' => 'Municipal',
                'business_address' => '15 Trade St, Abuja',
                'phone' => '08012345678',
                'full_name' => 'John Doe',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'Nigerian',
                'residential_address' => '15 Trade St, Abuja',
                'id_type' => 'nin',
                'id_number' => '12345678901',
                'registration_number' => 'RC789012',
                'tax_number' => 'TIN67890',
                'bvn' => '11111111111',
                'nin' => '22222222222',
                'bank_name' => 'Access Bank',
                'account_number' => '9876543210',
                'account_name' => 'New Seller Co',
                'trade_capacity' => '5,000 units',
                'cac_document' => UploadedFile::fake()->create('cac.pdf', 100),
                'valid_id' => UploadedFile::fake()->create('id.pdf', 100),
                'proof_of_address' => UploadedFile::fake()->create('address.pdf', 100),
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue($user->hasRole('seller'));

        $profile = SellerProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('pending', $profile->verification_status);
        $this->assertEquals('Nigeria', $profile->country);
        $this->assertEquals('Abuja', $profile->state);

        $documents = Document::where('owner_type', 'seller')
            ->where('owner_id', $profile->id)
            ->get();
        $this->assertCount(3, $documents);
    }

    public function test_seller_onboarding_requires_required_fields(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('seller.onboarding.store'), []);

        $response->assertSessionHasErrors([
            'business_name', 'business_category', 'business_address', 'country', 'state',
            'phone', 'full_name', 'date_of_birth', 'nationality', 'residential_address',
            'id_type', 'id_number',
        ]);
    }

    public function test_admin_can_promote_buyer_to_seller(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $buyer = User::factory()->create();
        $buyer->assignRole('buyer');

        $this->assertTrue($buyer->hasRole('buyer'));
        $this->assertFalse($buyer->hasRole('seller'));

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.buyers.become-seller', $buyer->id));

        $response->assertSessionHas('success');

        $buyer->refresh();
        $this->assertFalse($buyer->hasRole('buyer'));
        $this->assertTrue($buyer->hasRole('seller'));
    }

    public function test_become_seller_fails_for_non_buyer(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $seller = User::factory()->create();
        $seller->assignRole('seller');

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.buyers.become-seller', $seller->id));

        $response->assertSessionHas('error');
        $seller->refresh();
        $this->assertTrue($seller->hasRole('seller'));
    }
}
