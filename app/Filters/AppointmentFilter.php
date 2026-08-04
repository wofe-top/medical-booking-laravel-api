<?php

namespace App\Filters;



class AppointmentFilter extends QueryFilter
{
    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function appointment_date($value)
    {
        return $this->builder->where('appointment_date', $value);
    }

    public function date($value)
    {
        return $this->appointment_date($value);
    }

    public function doctor_id($value)
    {
        return $this->builder->where('doctor_profile_id', $value);
    }

    public function patient_id($value)
    {
        return $this->builder->where('patient_id', $value);
    }
}
