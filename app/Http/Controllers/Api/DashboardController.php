<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $summary = $this->dashboardService->getSummary();

        return response()->json([
            'message' => 'Dashboard summary fetched successfully',
            'data' => $summary,
        ], 200);
    }
}
