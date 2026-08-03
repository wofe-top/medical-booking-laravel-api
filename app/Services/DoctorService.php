<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\DoctorSchedule;
use App\Models\DoctorProfile;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\User;
use App\Http\Resources\DoctorResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Enums\AppointmentStatus;


class DoctorService
{
    public function index($filters, int $perPage = 10)
    {
        $doctors = DoctorProfile::with(['user', 'specialties'])
            ->filter($filters)
            ->paginate($perPage);

        return DoctorResource::collection($doctors);
    }

    public function store(array $data): DoctorProfile
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'doctor',
            ]);

            $doctor = $user->doctorProfile()->create([
                'experience_years' => $data['experience_years'],
                'consultation_fee' => $data['consultation_fee'],
                'bio' => $data['bio'] ?? null,
            ]);

            if (!empty($data['specialties'])) {
                $doctor->specialties()->sync($data['specialties']);
            }

            if (!empty($data['doctor_schedule']) && is_array($data['doctor_schedule'])) {
                foreach ($data['doctor_schedule'] as $shift) {
                    DoctorSchedule::create([
                        'doctor_profile_id' => $doctor->id,
                        'day_of_week' => $shift['day_of_week'],
                        'start_time' => $shift['start_time'],
                        'end_time' => $shift['end_time'],
                    ]);
                }
            }

            return $doctor;
        });
    }

    public function calculateAvailableSlots(DoctorProfile $doctorProfile, string $date, int $slotDurationMinutes = 30)
    {
        $dayOfWeek = date('w', strtotime($date));
        $schedule = DoctorSchedule::where('doctor_profile_id', $doctorProfile->id)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return [];
        }

        $bookedAppointments = Appointment::where('doctor_profile_id', $doctorProfile->id)
            ->where('appointment_date', $date)
            ->where('status', '!=', AppointmentStatus::CANCELLED)
            ->get(['start_time', 'end_time']);


        $availableSlots = [];

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        while ($startTime->copy()->addMinutes($slotDurationMinutes)->lte($endTime)) {

            $slotStart = $startTime->format('H:i:s');
            $startTime->addMinutes($slotDurationMinutes);
            $slotEnd = $startTime->format('H:i:s');


            $isOverlap = false;
            foreach ($bookedAppointments as $appointment) {
                if ($slotStart < $appointment->end_time && $slotEnd > $appointment->start_time) {
                    $isOverlap = true;
                    break;
                }
            }


            if (!$isOverlap) {
                $availableSlots[] = [
                    'start_time' => date('H:i', strtotime($slotStart)),
                    'end_time'   => date('H:i', strtotime($slotEnd)),
                ];
            }
        }

        return $availableSlots;
    }


    // for using in unit test
    public function getAvailableSlotsFromRawData(string $startTime, string $endTime, array $bookedAppointments, int $slotDurationMinutes = 30): array
    {
        $availableSlots = [];
        $startTimeCarbon = Carbon::parse($startTime);
        $endTimeCarbon = Carbon::parse($endTime);

        while ($startTimeCarbon->copy()->addMinutes($slotDurationMinutes)->lte($endTimeCarbon)) {

            $slotStart = $startTimeCarbon->format('H:i:s');
            $startTimeCarbon->addMinutes($slotDurationMinutes);
            $slotEnd = $startTimeCarbon->format('H:i:s');

            $isOverlap = false;
            foreach ($bookedAppointments as $appointment) {
                if ($slotStart < $appointment['end_time'] && $slotEnd > $appointment['start_time']) {
                    $isOverlap = true;
                    break;
                }
            }

            if (!$isOverlap) {
                $availableSlots[] = [
                    'start_time' => date('H:i', strtotime($slotStart)),
                    'end_time'   => date('H:i', strtotime($slotEnd)),
                ];
            }
        }

        return $availableSlots;
    }
}
