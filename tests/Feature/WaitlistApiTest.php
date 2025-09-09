<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Waitlist;

class WaitlistApiTest extends TestCase
{
    use RefreshDatabase;

    protected $token = 'STATIC_WAITLIST_TOKEN';

    public function test_cannot_access_without_token()
    {
        $response = $this->getJson('/api/waitlist');
        $response->assertStatus(401);
    }

    public function test_can_get_waitlist_with_token()
    {
        Waitlist::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'interest' => 'Test',
            'submitted_at' => now(),
        ]);
        $response = $this->getJson('/api/waitlist', [
            'X-API-TOKEN' => $this->token
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Test User']);
    }

    public function test_can_post_to_waitlist_with_token()
    {
        $data = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'phone' => '9876543210',
            'interest' => 'Interest',
        ];
        $response = $this->postJson('/api/waitlist', $data, [
            'X-API-TOKEN' => $this->token
        ]);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'New User']);
        $this->assertDatabaseHas('waitlist', ['email' => 'new@example.com']);
    }
}
