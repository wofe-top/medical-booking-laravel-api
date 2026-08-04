<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $appointmentDate = $this->appointment_date?->format('Y-m-d');

        return [
            'id' => $this->id,
            'patient_name' => $this->patient?->name,
            'doctor_name' => $this->doctorProfile?->user?->name,
            'appointment_date' => $appointmentDate,
            'appointment_date_label' => $this->appointment_date?->format('d M Y'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'time_label' => sprintf('%s - %s',
                $this->formatTime($this->start_time),
                $this->formatTime($this->end_time)
            ),
            'status' => $this->status,
            'status_label' => $this->status?->label(),
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
        ];
    }

    private function formatTime(string $time): string
    {
        return date('h:i A', strtotime($time));
    }
}
