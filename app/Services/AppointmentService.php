<?php

namespace App\Services;

use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use App\Models\DoctorSchedule;
use App\Exceptions\BusinessException;

use App\Enums\AppointmentStatus;


use App\Interfaces\NotificationServiceInterface;

class AppointmentService
{



    public function __construct(protected NotificationServiceInterface $notificationService) {}

    public function index($filters, int $perPage = 10)
    {
        $appointments = Appointment::with(['patient', 'doctorProfile.user'])
            ->filter($filters)
            ->paginate($perPage);

        return $appointments;
    }


    public function store(array $data): Appointment
    {

        $dayOfWeek = date('w', strtotime($data['appointment_date']));



        $isAvailable = DoctorSchedule::where('doctor_profile_id', $data['doctor_profile_id'])
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $data['start_time'])
            ->where('end_time', '>=', $data['end_time'])
            ->exists();


        if (!$isAvailable) {
            throw new BusinessException(__('The doctor does not work during these hours or on this day.'));
        }


        $hasOverlap = Appointment::where('doctor_profile_id', $data['doctor_profile_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('status', '!=', AppointmentStatus::CANCELLED)
            ->where(function ($query) use ($data) {
                $query->where('start_time', '<', $data['end_time'])
                    ->where('end_time', '>', $data['start_time']);
            })
            ->exists();

        if ($hasOverlap) {
            throw new BusinessException(__('This time slot is already booked for this doctor.'));
        }

        $this->notificationService->sendNotification($data['patient_id'], 'You have a new appointment');

        return DB::transaction(function () use ($data) {
            return Appointment::create([
                'patient_id' => $data['patient_id'],
                'doctor_profile_id' => $data['doctor_profile_id'],
                'appointment_date' => $data['appointment_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function cancel(Appointment $appointment,
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): Appointment {
        if ($appointment->status === AppointmentStatus::CANCELLED) {
            throw new BusinessException(__('The appointment has already been cancelled.'));
        }

        if ($appointment->status === AppointmentStatus::COMPLETED) {
            throw new BusinessException(__('Completed appointments cannot be cancelled.'));
        }

        if ($user->role === 'patient' && $appointment->patient_id !== $user->id) {
            throw new BusinessException(__('You can only cancel your own appointments.'));
        }

        if ($user->role === 'doctor') {
            $appointment->loadMissing('doctorProfile');

            if (!$appointment->doctorProfile || $appointment->doctorProfile->user_id !== $user->id) {
                throw new BusinessException(__('You can only cancel appointments for your own doctor profile.'));
            }
        }

        if (!in_array($user->role, ['patient', 'doctor'], true)) {
            throw new BusinessException(__('You are not authorized to cancel this appointment.'));
        }

        return DB::transaction(function () use ($appointment) {
            $appointment->update(['status' => AppointmentStatus::CANCELLED]);

            $appointment->loadMissing(['patient', 'doctorProfile.user']);

            $patientRecipient = $appointment->patient->phone ?? $appointment->patient->email ?? (string) $appointment->patient_id;
            $doctorRecipient = $appointment->doctorProfile->user->phone ?? $appointment->doctorProfile->user->email ?? (string) $appointment->doctor_profile_id;

            $this->notificationService->sendNotification($patientRecipient, 'Your appointment has been cancelled.');
            $this->notificationService->sendNotification($doctorRecipient, 'An appointment has been cancelled.');

            return $appointment;
        });
    }
}
