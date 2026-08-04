<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\UserResource;

class UserService
{
    public function index($filters, int $perPage = 10)
    {
        $users = User::where('role', 'patient')
            ->filter($filters)
            ->paginate($perPage);

        return UserResource::collection($users);
    }
}
