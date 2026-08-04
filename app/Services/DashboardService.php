<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\User;

class DashboardService
{
    public function getSummary(): array
    {
        return [
            'doctors_count' => DoctorProfile::count(),
            'patients_count' => User::where('role', 'patient')->count(),
            'appointments_count' => Appointment::count(),
        ];
    }
}
