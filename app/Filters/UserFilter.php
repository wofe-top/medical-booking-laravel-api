<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    public function name($value)
    {
        return $this->builder->where('name', 'like', "%{$value}%");
    }

    public function email($value)
    {
        return $this->builder->where('email', 'like', "%{$value}%");
    }
}
