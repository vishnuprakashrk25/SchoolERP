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
     *      @OA\RequestBody(required=true, @OA\JsonContent(required={"name", "phone"}, type="object")),
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

        $log = PhoneCallLog::create($request->all());

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
