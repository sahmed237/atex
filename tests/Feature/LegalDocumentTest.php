<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\LegalDocument;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LegalDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles/permissions exist for testing
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::findOrCreate('manage legal documents', 'web');
        Permission::findOrCreate('view legal documents', 'web');
        $role = Role::findOrCreate('super-admin', 'web');
        $role->givePermissionTo(Permission::all());
    }

    public function test_admin_can_view_legal_documents()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->create([
            'is_active' => true,
        ]);
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->get('/admin/legal-documents');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_legal_document()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->post(route('admin.legal-documents.store'), [
            'document_type' => 'terms',
            'title' => 'Terms of Service',
            'description' => 'Our terms.',
            'version' => '1.0',
            'effective_date' => now()->format('Y-m-d'),
            'content' => '<p>These are the terms</p>',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('legal_documents', [
            'document_type' => 'terms',
            'title' => 'Terms of Service',
        ]);
        $this->assertDatabaseHas('legal_document_versions', [
            'version' => '1.0',
            'is_active' => 1,
        ]);
    }

    public function test_admin_can_add_version_and_it_generates_hash()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $document = LegalDocument::create([
            'document_type' => 'terms',
            'title' => 'Terms of Service'
        ]);

        $response = $this->actingAs($admin)->post("/admin/legal-documents/{$document->id}/versions", [
            'version' => '1.0',
            'content' => '<p>Hello world</p>',
            'effective_date' => now()->toDateString(),
            'is_active' => true,
        ]);

        $response->assertRedirect();
        
        $version = $document->versions()->first();
        $this->assertNotNull($version);
        $this->assertEquals('1.0', $version->version);
        $this->assertTrue($version->is_active);
        $this->assertNotEmpty($version->content_hash);
        $this->assertEquals(hash('sha256', '<p>Hello world</p>'), $version->content_hash);
    }

    public function test_user_is_redirected_to_acceptance_page()
    {
        $admin = User::factory()->create();
        $document = LegalDocument::create([
            'document_type' => 'terms',
            'title' => 'Terms of Service'
        ]);
        $document->versions()->create([
            'version' => '1.0',
            'content' => 'Content',
            'effective_date' => now(),
            'is_active' => true,
            'created_by' => $admin->id
        ]);

        $user = User::factory()->create();
        Role::findOrCreate('buyer', 'web');
        $user->assignRole('buyer');
        \App\Models\BuyerProfile::create(['user_id' => $user->id, 'verification_status' => 'approved']);
        
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('legal-acceptance.show'));
    }

    public function test_user_can_accept_document_and_proceed()
    {
        $admin = User::factory()->create();
        $document = LegalDocument::create([
            'document_type' => 'terms',
            'title' => 'Terms of Service'
        ]);
        $version = $document->versions()->create([
            'version' => '1.0',
            'content' => 'Content',
            'effective_date' => now(),
            'is_active' => true,
            'created_by' => $admin->id
        ]);

        $user = User::factory()->create();
        Role::findOrCreate('buyer', 'web');
        $user->assignRole('buyer');
        \App\Models\BuyerProfile::create(['user_id' => $user->id, 'verification_status' => 'approved']);
        
        $response = $this->actingAs($user)->post('/legal-acceptance', [
            'documents' => [$version->id]
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('user_document_acceptances', [
            'user_id' => $user->id,
            'legal_document_version_id' => $version->id,
        ]);

        // Second time they should not be redirected
        $response = $this->actingAs($user)->get('/dashboard');
        // Actually, depending on the role, they get redirected to seller/buyer dashboard, but not legal-acceptance
        $response->assertRedirect(); // should not be legal-acceptance
        $this->assertNotEquals(route('legal-acceptance.show'), $response->headers->get('Location'));
    }
}
