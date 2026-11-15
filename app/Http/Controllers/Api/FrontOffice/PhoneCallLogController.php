<?php

namespace App\Http\Controllers\Api\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\PhoneCallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneCallLogController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/front-office/phone-call-logs",
     *      operationId="getPhoneCallLogsList",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Get list of phone call logs",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => PhoneCallLog::latest()->get()
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/front-office/phone-call-logs",
     *      operationId="storePhoneCallLog",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Store new phone call log",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "phone"},
     *              @OA\Property(property="name", type="string", example="Salim Kumar"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-15"),
     *              @OA\Property(property="description", type="string", example="Inquiry about Class 1 admission criteria"),
     *              @OA\Property(property="next_follow_up_date", type="string", format="date", example="2026-11-20"),
     *              @OA\Property(property="call_duration", type="string", example="05:30"),
     *              @OA\Property(property="note", type="string", example="Parent will visit school office on Friday"),
     *              @OA\Property(property="call_type", type="string", example="Incoming")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }
        if (!empty($data['next_follow_up_date']) && empty($data['follow_up_date'])) {
            $data['follow_up_date'] = $data['next_follow_up_date'];
        } elseif (!empty($data['follow_up_date']) && empty($data['next_follow_up_date'])) {
            $data['next_follow_up_date'] = $data['follow_up_date'];
        }

        $log = PhoneCallLog::create($data);

        return response()->json(['success' => true, 'data' => $log], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/front-office/phone-call-logs/{id}",
     *      operationId="getPhoneCallLogById",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Get phone call log details",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        return response()->json([
            'success' => true,
            'data' => PhoneCallLog::findOrFail($id)
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/front-office/phone-call-logs/{id}",
     *      operationId="updatePhoneCallLog",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Update phone call log",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=false,
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Salim Kumar"),
     *              @OA\Property(property="phone", type="string", example="+91 9876543210"),
     *              @OA\Property(property="date", type="string", format="date", example="2026-11-15"),
     *              @OA\Property(property="description", type="string", example="Follow-up on admission query"),
     *              @OA\Property(property="next_follow_up_date", type="string", format="date", example="2026-11-22"),
     *              @OA\Property(property="call_duration", type="string", example="03:45"),
     *              @OA\Property(property="note", type="string", example="Parent confirmed visit on Monday"),
     *              @OA\Property(property="call_type", type="string", example="Outgoing")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $log = PhoneCallLog::findOrFail($id);
        $data = $request->all();
        if (!empty($data['next_follow_up_date']) && empty($data['follow_up_date'])) {
            $data['follow_up_date'] = $data['next_follow_up_date'];
        } elseif (!empty($data['follow_up_date']) && empty($data['next_follow_up_date'])) {
            $data['next_follow_up_date'] = $data['follow_up_date'];
        }
        $log->update($data);
        return response()->json(['success' => true, 'message' => 'Phone call log updated successfully', 'data' => $log]);
    }

    /**
     * @OA\Delete(
     *      path="/api/front-office/phone-call-logs/{id}",
     *      operationId="deletePhoneCallLog",
     *      tags={"1. Reception & Admission - Front Office"},
     *      summary="Delete phone call log",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id)
    {
        PhoneCallLog::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
