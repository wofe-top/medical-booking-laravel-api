<?php

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AppointmentFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_date_filter_returns_only_matching_records(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctor->id]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_profile_id' => $doctorProfile->id,
            'appointment_date' => '2026-08-05',
            'start_time' => '09:00',
            'end_time' => '09:30',
            'notes' => 'Matching appointment',
        ]);

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_profile_id' => $doctorProfile->id,
            'appointment_date' => '2026-07-20',
            'start_time' => '10:00',
            'end_time' => '10:30',
            'notes' => 'Non-matching appointment',
        ]);

        Sanctum::actingAs($patient, ['*']);

        $this->assertDatabaseCount('appointments', 2);
        $this->assertSame(1, Appointment::where('appointment_date', '2026-08-05')->count());

        $response = $this->getJson('/api/appointments?appointment_date=2026-08-05');

        dump($response->json());
        $response->assertStatus(200);
        $response->assertJsonPath('meta.total', 1);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.appointment_date', '2026-08-05');
    }
}
