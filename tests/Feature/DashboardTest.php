<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_counts_for_doctors_patients_and_appointments(): void
    {
        $patientA = User::factory()->create(['role' => 'patient']);
        User::factory()->create(['role' => 'patient']);
        DoctorProfile::factory()->create();

        Appointment::create([
            'patient_id' => $patientA->id,
            'doctor_profile_id' => DoctorProfile::factory()->create()->id,
            'appointment_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '09:30',
            'notes' => 'Appointment one',
        ]);

        Appointment::create([
            'patient_id' => $patientA->id,
            'doctor_profile_id' => DoctorProfile::factory()->create()->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'notes' => 'Appointment two',
        ]);

        Sanctum::actingAs($patientA, ['*']);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['doctors_count', 'patients_count', 'appointments_count'],
        ]);
        $response->assertJsonPath('data.doctors_count', 3);
        $response->assertJsonPath('data.patients_count', 2);
        $response->assertJsonPath('data.appointments_count', 2);
    }
}
