<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    /** @test */
    public function user_can_update_purchase_balance()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $asset = Asset::factory()->create([
            'closing_balance' => 10,
            'opening_balance' => 10,
            'purchases' => 0,
            'net_movements' => 0,
        ]);

        $response = $this->postJson("/api/assets/{$asset->id}/purchase", [
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'transaction', 'asset'])
            ->assertJson([
                'asset' => [
                    'closing_balance' => 15,
                    'purchases' => 5,
                    'net_movements' => 5,
                ]
            ]);
    }

    /** @test */
    public function purchase_balance_update_requires_quantity()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $asset = Asset::factory()->create();

        $response = $this->postJson("/api/assets/{$asset->id}/purchase", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['quantity']);
    }
}
