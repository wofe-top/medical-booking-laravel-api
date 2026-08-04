<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Filters\UserFilter;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * @authenticated
     */
    public function index(Request $request, UserFilter $filters)
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $result = $this->userService->index($filters, $perPage);

        return $result->additional([
            'message' => 'Patients Fetched Successfully'
        ]);
    }
}
