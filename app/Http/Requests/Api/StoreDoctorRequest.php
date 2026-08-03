<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'bio' => ['nullable'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['integer', 'exists:specialties,id'],
            'doctor_schedule' => ['nullable', 'array'],
            'doctor_schedule.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'doctor_schedule.*.start_time' => ['required', 'date_format:H:i'],
            'doctor_schedule.*.end_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'specialties.*.exists' => __('The selected specialty is invalid.'),
        ];
    }
}
