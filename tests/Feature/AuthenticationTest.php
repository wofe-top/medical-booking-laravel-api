<?php


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;

use Illuminate\Support\Facades\Hash;


class AuthenticationTest extends TestCase
{


    use RefreshDatabase;
    public function test_patient_can_register_successfuly(): void
    {

        // step-1-Arrange
        $payload = [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'phone'                 => '123456789',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'patient',
        ];
        // step-2-Act
        $response = $this->postJson('/api/auth/register', $payload);
        // step-3-Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'updated_at', 'created_at'],
            'token',
            'message'
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'role'  => 'patient'
        ]);
    }


    public function test_doctor_can_register_with_profile_and_schedule(): void
    {


        $payload = [
            'name'                  => 'Dr. Smith',
            'email'                 => 'smith@example.com',
            'phone'                 => '987654321',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'doctor',
            'experience_years'      => 8,
            'consultation_fee'      => 150.00,
            'bio'                   => 'Cardiology specialist',
            'doctor_schedule'       => [
                [
                    'day_of_week' => 1,
                    'start_time'  => '09:00',
                    'end_time'    => '17:00'
                ]
            ]
        ];


        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(200);



        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'updated_at', 'created_at'],
            'token',
            'message'
        ]);


        $this->assertDatabaseHas('users', [
            'email' => 'smith@example.com',
            'role'  => 'doctor'
        ]);

        $this->assertDatabaseHas('doctor_profiles', [
            'experience_years' => 8,
            'consultation_fee' => 150.00
        ]);

        $this->assertDatabaseHas('doctor_schedules', [
            'day_of_week' => 1,
            'start_time'  => '09:00'
        ]);
    }


    public function test_user_can_login(): void
    {

        User::create([
            'name'     => 'John Doe',
            'email'    => 'fvon@example.org',
            'password' => Hash::make('password'),
            'role'     => 'patient',
        ]);



        $payload = [
            "email" => "fvon@example.org",
            "password" => "password"
        ];

        $response = $this->postJson('/api/auth/login', $payload);




        $response->assertStatus(200);

        $response->assertJsonStructure([
            "message",
            "token",
            "user" => ["id", "name", "email", "email_verified_at", "role", "phone", "updated_at", "created_at"]
        ]);
    }

    public function test_authenticated_user_can_fetch_their_profile_via_user_endpoint(): void
    {
        $user = User::create([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.org',
            'password' => Hash::make('password'),
            'role'     => 'patient',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at']
        ]);
        $response->assertJsonPath('user.id', $user->id);
    }
}
