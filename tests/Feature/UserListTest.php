<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_endpoint_returns_only_patients_with_pagination(): void
    {
        User::factory()->count(3)->create(['role' => 'patient']);
        User::factory()->count(2)->create(['role' => 'doctor']);

        $user = User::factory()->create(['role' => 'patient']);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/users?per_page=2');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
        $response->assertJsonPath('meta.per_page', 2);
        $response->assertJsonPath('meta.total', 4);

        foreach ($response->json('data') as $patient) {
            $this->assertSame('patient', $patient['role']);
        }
    }
}
