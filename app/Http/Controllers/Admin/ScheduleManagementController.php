<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ScheduleManagementRequest;
use App\Interface\ScheduleManagementInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ScheduleManagementController extends Controller
{
    private $scheduleManagementRepo, $scheduleManagementRequest;

    public function __construct(ScheduleManagementInterface $scheduleManagementRepo, ScheduleManagementRequest $scheduleManagementRequest)
    {
        $this->middleware('auth:api');
        $this->scheduleManagementRepo = $scheduleManagementRepo;
        $this->scheduleManagementRequest = $scheduleManagementRequest;
    }

    /**
     *  @OA\Get(
     *  path="/admin/schedules",
     *  tags={"Schedule Management"},
     *  summary="Get All Schedules",
     *  security={{"bearerAuth":{}}},
     *  description="Retrieve all schedules",
     *     @OA\Response(
     *         response=201,
     *         description="Class created successfully",
     *         @OA\JsonContent(type="object")
     *     ),
     *  @OA\Response(
     *      response=404,
     *      description="User not found"
     *  )
     * )
     */
    public function index()
    {
        try {
            $schedules = $this->scheduleManagementRepo->index();
            return response()->json(['schedules' => $schedules], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve schedules', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/admin/schedule",
     *     tags={"Schedule Management"},
     *     summary="Create a New Schedule",
     *     security={{"bearerAuth":{}}},
     *     description="Add a new schedule with specific days and time slots.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"days", "start_time", "end_time"},
     *             @OA\Property(
     *                 property="days",
     *                 type="array",
     *                 description="Days of the schedule",
     *                 @OA\Items(type="string", enum={"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"}, example="Monday")
     *             ),
     *             @OA\Property(property="start_time", type="string", format="time", example="08:00:00", description="Start time of the schedule"),
     *             @OA\Property(property="end_time", type="string", format="time", example="16:00:00", description="End time of the schedule (must be after start_time)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Schedule created successfully",
     *         @OA\JsonContent(type="object", example={"message": "Schedule created successfully"})
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

    public function create()
    {
        try {
            // Validation
            $validator = $this->scheduleManagementRequest->store();
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $schedule = $this->scheduleManagementRepo->store($validated_payload);

            return response()->json(['message' => 'Schedule created successfully', 'schedule' => $schedule], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create schedule', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/admin/schedule/{id}",
     *     tags={"Schedule Management"},
     *     summary="Update an Existing Schedule",
     *     security={{"bearerAuth":{}}},
     *     description="Update the details of an existing schedule.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the schedule to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"days", "start_time", "end_time"},
     *             @OA\Property(
     *                 property="days",
     *                 type="array",
     *                 description="Days of the schedule",
     *                 @OA\Items(type="string", enum={"Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"}, example="Tuesday")
     *             ),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00", description="Start time of the schedule"),
     *             @OA\Property(property="end_time", type="string", format="time", example="17:00:00", description="End time of the schedule (must be after start_time)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule updated successfully",
     *         @OA\JsonContent(type="object", example={"message": "Schedule updated successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     )
     * )
     */

    public function update($id)
    {
        try {
            // Validation
            $validator = $this->scheduleManagementRequest->update($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $schedule = $this->scheduleManagementRepo->update($validated_payload);

            return response()->json(['message' => 'Schedule updated successfully', 'schedule' => $schedule], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update schedule', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/admin/schedule/{id}",
     *     tags={"Schedule Management"},
     *     summary="Delete a Schedule",
     *     security={{"bearerAuth":{}}},
     *     description="Delete an existing schedule by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the schedule to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule deleted successfully",
     *         @OA\JsonContent(type="object", example={"message": "Schedule deleted successfully"})
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     )
     * )
     */

    public function delete($id)
    {
        try {
            // Validation
            $validator = $this->scheduleManagementRequest->delete($id);
            if ($validator->fails())
                return response()->json($validator->errors()->first());

            $validated_payload = (object) $validator->validated();

            $schedule = $this->scheduleManagementRepo->delete($validated_payload);

            return response()->json(['message' => 'Schedule deleted successfully', 'schedule' => $schedule], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete schedule', 'message' => $e->getMessage()], 500);
        }
    }
}
