<?php

namespace App\Filters;



class DoctorProfileFilter extends QueryFilter
{
    public function experience_years($value)
    {
        return $this->builder->where('experience_years', '>=', $value);
    }

    public function name($value)
    {
        return $this->builder->whereHas('user', function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%");
        });
    }

    public function email($value)
    {
        return $this->builder->whereHas('user', function ($query) use ($value) {
            $query->where('email', 'like', "%{$value}%");
        });
    }

    public function specialty_id($value)
    {
        return $this->builder->whereHas('specialties', function ($query) use ($value) {

            $query->where('specialties.id', $value);
        });
    }
}
