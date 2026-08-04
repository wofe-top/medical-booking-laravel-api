<?php

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DoctorListTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctors_endpoint_returns_paginated_doctors(): void
    {
        DoctorProfile::factory()->count(3)->create();
        $user = User::factory()->create(['role' => 'patient']);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/doctors?per_page=2');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
        $response->assertJsonPath('meta.per_page', 2);
        $response->assertJsonPath('meta.total', 3);
    }
}
