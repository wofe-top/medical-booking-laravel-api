<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Filters\AppointmentFilter;
use App\Http\Resources\AppointmentResource;

use App\Services\AppointmentService;
use App\Http\Requests\Api\StoreAppointmentRequest;
use App\Http\Requests\Api\CancelAppointmentRequest;

use Illuminate\Http\Request;

class AppointmentController extends Controller
{


    public function __construct(
        protected AppointmentService $appointmentService
    ) {}




    /**
     *
     *
     * @authenticated
     */
    public function index(Request $request, AppointmentFilter $filters)
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $appointments = $this->appointmentService->index($filters, $perPage);

        return AppointmentResource::collection($appointments)->additional([
            'message' => 'Appointments Fetched Successfully'
        ]);
    }

    /**
     *
     *
     * @authenticated
     */
    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->appointmentService->store($request->validated());

        return (new AppointmentResource($appointment))->additional([
            'message' => 'Appointment Created Successfully'
        ]);
    }

    /**
     * @authenticated
     */
    public function cancel(CancelAppointmentRequest $request, Appointment $appointment)
    {
        $appointment = $this->appointmentService->cancel($appointment, $request->user());

        return (new AppointmentResource($appointment))->additional([
            'message' => 'Appointment Cancelled Successfully'
        ]);
    }
}
