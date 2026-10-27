<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BehaviourSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BehaviourSettingController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/behaviour-settings",
     *      operationId="getBehaviourSettings",
     *      tags={"Student Behaviour - Settings"},
     *      summary="Get behaviour record settings (Comment & View toggles)",
     *      description="Fetches current behaviour settings for student comment, parent comment, student view, and parent view.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="student_comment", type="boolean", example=false),
     *                  @OA\Property(property="parent_comment", type="boolean", example=true),
     *                  @OA\Property(property="student_view", type="boolean", example=true),
     *                  @OA\Property(property="parent_view", type="boolean", example=false)
     *              )
     *          )
     *      )
     * )
     */
    public function show(Request $request)
    {
        $branchId = $request->user() ? $request->user()->branch_id : null;
        
        $settings = BehaviourSetting::where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            }
        })->first();

        if (!$settings) {
            $settings = BehaviourSetting::create([
                'branch_id'       => $branchId,
                'student_comment' => false,
                'parent_comment'  => true,
                'student_view'    => true,
                'parent_view'     => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/behaviour-settings",
     *      operationId="updateBehaviourSettings",
     *      tags={"Student Behaviour - Settings"},
     *      summary="Update behaviour record settings",
     *      description="Update comment and view toggles for students and parents.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="student_comment", type="boolean", example=false),
     *              @OA\Property(property="parent_comment", type="boolean", example=true),
     *              @OA\Property(property="student_view", type="boolean", example=true),
     *              @OA\Property(property="parent_view", type="boolean", example=false)
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Settings updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Behaviour settings updated successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_comment' => 'nullable|boolean',
            'parent_comment'  => 'nullable|boolean',
            'student_view'    => 'nullable|boolean',
            'parent_view'     => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $branchId = $request->user() ? $request->user()->branch_id : null;

        $settings = BehaviourSetting::where(function ($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            }
        })->first();

        if (!$settings) {
            $settings = new BehaviourSetting(['branch_id' => $branchId]);
        }

        if ($request->has('student_comment')) {
            $settings->student_comment = filter_var($request->input('student_comment'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('parent_comment')) {
            $settings->parent_comment = filter_var($request->input('parent_comment'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('student_view')) {
            $settings->student_view = filter_var($request->input('student_view'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('parent_view')) {
            $settings->parent_view = filter_var($request->input('parent_view'), FILTER_VALIDATE_BOOLEAN);
        }

        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Behaviour settings updated successfully',
            'data'    => $settings,
        ]);
    }
}
