<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GeneralSettingsController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/general-settings",
     *      operationId="getGeneralSettings",
     *      tags={"Settings"},
     *      summary="Retrieve the application general settings",
     *      description="Returns the school's general configurations. If no row exists, it initializes it automatically with empty defaults.",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function get()
    {
        $settings = GeneralSetting::first();

        if (!$settings) {
            $settings = GeneralSetting::create([
                'school_name' => 'My School',
                'site_title' => 'School ERP',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * @OA\Put(
     *      path="/api/general-settings",
     *      operationId="updateGeneralSettings",
     *      tags={"Settings"},
     *      summary="Update the application general settings",
     *      description="Validates and updates the global settings row.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"school_name", "site_title"},
     *              @OA\Property(property="school_name", type="string", example="RAZA UL ULOOM ISLAMIA HIGHER SECONDARY SCHOOL POONCH"),
     *              @OA\Property(property="site_title", type="string", example="RUIHSS POONCH"),
     *              @OA\Property(property="address", type="string", example="Parade Ground, Old City, Poonch, Jammu and Kashmir 185101"),
     *              @OA\Property(property="state", type="string", example="Jammu and Kashmir"),
     *              @OA\Property(property="phone", type="string", example="+917298010134"),
     *              @OA\Property(property="email", type="string", example="admin@ruihss.in"),
     *              @OA\Property(property="fees_income_head", type="string", example="Annual Fees Collection"),
     *              @OA\Property(property="school_code", type="string", example="01110902404"),
     *              @OA\Property(property="suic_code", type="string", example="SUIC12345"),
     *              @OA\Property(property="zone", type="string", example="Poonch Zone 1"),
     *              @OA\Property(property="category_of_institution", type="string", example="Higher Secondary"),
     *              @OA\Property(property="academic_year_id", type="integer", example=1),
     *              @OA\Property(property="language", type="string", example="English"),
     *              @OA\Property(property="date_format", type="string", example="dd/mm/yyyy"),
     *              @OA\Property(property="week_start_day", type="string", example="Monday"),
     *              @OA\Property(property="time_zone", type="string", example="Asia/Kolkata"),
     *              @OA\Property(property="currency", type="string", example="INR"),
     *              @OA\Property(property="currency_symbol", type="string", example="₹"),
     *              @OA\Property(property="max_upload_size", type="string", example="102400 MB"),
     *              @OA\Property(property="multiple_roll_number", type="string", example="Disable"),
     *              @OA\Property(property="promotion_without_exam", type="string", example="Enable"),
     *              @OA\Property(property="subject_attendance_layout", type="string", example="Layout 1"),
     *              @OA\Property(property="new_fees_module", type="string", example="Enable"),
     *              @OA\Property(property="result_type", type="string", example="100 %Mark"),
     *              @OA\Property(property="student_admission", type="string", example="With Guardian"),
     *              @OA\Property(property="due_fees_login_restriction", type="string", example="Disable"),
     *              @OA\Property(property="in_news_auto_approval_comment", type="string", example="Disable"),
     *              @OA\Property(property="in_news_can_comment", type="string", example="Disable"),
     *              @OA\Property(property="blog_search", type="string", example="Enable"),
     *              @OA\Property(property="recent_blog", type="string", example="Enable"),
     *              @OA\Property(property="carry_forward_fees_due_days", type="integer", example=60),
     *              @OA\Property(property="queue_connection", type="string", example="SYNC"),
     *              @OA\Property(property="copyright_text", type="string", example="Copyright © 2026 RUIHSS")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Settings updated successfully"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => 'required|string|max:255',
            'site_title' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'carry_forward_fees_due_days' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = GeneralSetting::first();

        if (!$settings) {
            $settings = new GeneralSetting();
        }

        $settings->fill($request->only([
            'school_name',
            'site_title',
            'address',
            'state',
            'phone',
            'email',
            'fees_income_head',
            'school_code',
            'suic_code',
            'zone',
            'category_of_institution',
            'academic_year_id',
            'language',
            'date_format',
            'week_start_day',
            'time_zone',
            'currency',
            'currency_symbol',
            'max_upload_size',
            'multiple_roll_number',
            'promotion_without_exam',
            'subject_attendance_layout',
            'new_fees_module',
            'result_type',
            'student_admission',
            'due_fees_login_restriction',
            'in_news_auto_approval_comment',
            'in_news_can_comment',
            'blog_search',
            'recent_blog',
            'carry_forward_fees_due_days',
            'queue_connection',
            'copyright_text',
        ]));

        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $settings
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/general-settings/logo",
     *      operationId="uploadSchoolLogo",
     *      tags={"Settings"},
     *      summary="Upload the school logo",
     *      description="Handles uploading and saving the logo image.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"logo"},
     *                  @OA\Property(property="logo", type="string", format="binary")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful upload",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="logo_url", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function uploadLogo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = GeneralSetting::first();
        if (!$settings) {
            $settings = GeneralSetting::create([
                'school_name' => 'My School',
                'site_title' => 'School ERP',
            ]);
        }

        if ($request->hasFile('logo')) {
            // Delete old logo file if exists
            if ($settings->getRawOriginal('logo')) {
                $oldPath = public_path(ltrim(parse_url($settings->getRawOriginal('logo'), PHP_URL_PATH), '/'));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            
            $urlPath = '/uploads/settings/' . $filename;
            $settings->logo = $urlPath;
            $settings->save();

            return response()->json([
                'success' => true,
                'logo_url' => url($urlPath),
                'data' => $settings
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/general-settings/favicon",
     *      operationId="uploadSchoolFavicon",
     *      tags={"Settings"},
     *      summary="Upload the school favicon",
     *      description="Handles uploading and saving the favicon image.",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"favicon"},
     *                  @OA\Property(property="favicon", type="string", format="binary")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful upload",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="favicon_url", type="string"),
     *              @OA\Property(property="data", type="object")
     *          )
     *      )
     * )
     */
    public function uploadFavicon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'favicon' => 'required|image|mimes:jpeg,png,jpg,gif,ico,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $settings = GeneralSetting::first();
        if (!$settings) {
            $settings = GeneralSetting::create([
                'school_name' => 'My School',
                'site_title' => 'School ERP',
            ]);
        }

        if ($request->hasFile('favicon')) {
            // Delete old favicon file if exists
            if ($settings->getRawOriginal('favicon')) {
                $oldPath = public_path(ltrim(parse_url($settings->getRawOriginal('favicon'), PHP_URL_PATH), '/'));
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('favicon');
            $filename = time() . '_fav_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            
            $urlPath = '/uploads/settings/' . $filename;
            $settings->favicon = $urlPath;
            $settings->save();

            return response()->json([
                'success' => true,
                'favicon_url' => url($urlPath),
                'data' => $settings
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No file uploaded'
        ], 400);
    }
}
