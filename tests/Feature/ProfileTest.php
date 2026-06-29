<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BuyerProfile;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function createBuyerUser()
    {
        $user = User::factory()->create();
        Role::findOrCreate('buyer', 'web');
        $user->assignRole('buyer');
        BuyerProfile::create(['user_id' => $user->id, 'verification_status' => 'approved']);
        return $user;
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createBuyerUser();

        $response = $this
            ->actingAs($user)
            ->get('/buyer/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createBuyerUser();

        $response = $this
            ->actingAs($user)
            ->put('/buyer/profile/info', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
    }
}
