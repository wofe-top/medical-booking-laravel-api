<?php

namespace App\Http\Controllers\Api;

use App\Models\DoctorProfile;
use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\SlotAvaliableDoctorResource;
use Illuminate\Http\Request;

use App\Filters\DoctorProfileFilter;

use App\Services\DoctorService;

use App\Http\Requests\Api\DoctorRequest;
use App\Http\Requests\Api\StoreDoctorRequest;


class DoctorController extends Controller
{


    public function __construct(
        protected DoctorService  $doctorService
    ) {}


    /**
 *
 *
 * @authenticated
 */
    public function index(Request $request, DoctorProfileFilter $filters)
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = $perPage > 0 ? min($perPage, 100) : 10;

        $result = $this->doctorService->index($filters, $perPage);

        return $result->additional([
            'message' => 'Doctors Fetched Successfully'
        ]);
    }

    /**
 *
 *
 * @authenticated
 */
    public function store(StoreDoctorRequest $request)
    {
        $doctor = $this->doctorService->store($request->validated());

        return (new DoctorResource($doctor->load(['user', 'specialties'])))->additional([
            'message' => 'Doctor Created Successfully'
        ]);
    }


    /**
 *
 *
 * @authenticated
 */
    public function getAvailableSlots(DoctorRequest $request, DoctorProfile $doctorProfile)
    {



        $slots = $this->doctorService->calculateAvailableSlots(
            $doctorProfile,
            $request->validated('date')
        );



        return SlotAvaliableDoctorResource::collection($slots)->additional([
            'message' => 'Slots Fetched Successfully'
        ]);
    }
}
