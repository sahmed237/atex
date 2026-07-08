<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetectUserCountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_location_is_nigeria_when_ip_not_found(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertEquals('NG', session('user_country'));
        $this->assertEquals('NGN', session('user_currency'));
    }

    public function test_manual_override_updates_session_and_currency(): void
    {
        $response = $this->postJson('/location/set', [
            'country' => 'US',
            'country_name' => 'United States',
            'currency' => 'USD',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'country' => 'US',
            'country_name' => 'United States',
            'currency' => 'USD',
        ]);

        $this->assertEquals('US', session('user_country'));
        $this->assertEquals('United States', session('user_country_name'));
        $this->assertEquals('USD', session('user_currency'));
    }

    public function test_manual_override_to_uk_sets_eur(): void
    {
        $response = $this->postJson('/location/set', [
            'country' => 'GB',
            'country_name' => 'United Kingdom',
            'currency' => 'EUR',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('GB', session('user_country'));
        $this->assertEquals('EUR', session('user_currency'));
    }
}
