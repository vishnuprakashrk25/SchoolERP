<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TwoFactorController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ClassTeacherController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\Admission\AdmissionQueryController;
use App\Http\Controllers\Api\FrontOffice\ComplaintController;
use App\Http\Controllers\Api\FrontOffice\PostalController;
use App\Http\Controllers\Api\FrontOffice\PhoneCallLogController;
use App\Http\Controllers\Api\ClassRoomController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\StudentCategoryController;
use App\Http\Controllers\Api\StudentParentController;
use App\Http\Controllers\Api\StudentDocumentController;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\StudentMultiClassController;
use App\Http\Controllers\Api\StaffDepartmentController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StaffAttendanceController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamScheduleController;
use App\Http\Controllers\Api\ExamResultController;
use App\Http\Controllers\Api\FeeTypeController;
use App\Http\Controllers\Api\FeeGroupController;
use App\Http\Controllers\Api\FeeStructureController;
use App\Http\Controllers\Api\StudentFeeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CertificateTemplateController;
use App\Http\Controllers\Api\GeneratedCertificateController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AssignmentSubmissionController;
use App\Http\Controllers\Api\StudyMaterialController;
use App\Http\Controllers\Api\DownloadContentTypeController;
use App\Http\Controllers\Api\DownloadContentController;
use App\Http\Controllers\Api\SharedContentController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MessageThreadController;
use App\Http\Controllers\Api\BulkPrintLogController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\TodoController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GoogleCalendarController;
use App\Http\Controllers\Api\GlobalSearchController;
use App\Http\Controllers\Api\GeneralSettingsController;





/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/2fa/setup', [TwoFactorController::class, 'setup']);
    Route::post('/2fa/confirm', [TwoFactorController::class, 'confirm']);
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Global Search
    Route::get('/search', [GlobalSearchController::class, 'search']);

    // General Settings
    Route::get('/general-settings', [GeneralSettingsController::class, 'get']);
    Route::put('/general-settings', [GeneralSettingsController::class, 'update']);
    Route::post('/general-settings/logo', [GeneralSettingsController::class, 'uploadLogo']);
    Route::post('/general-settings/favicon', [GeneralSettingsController::class, 'uploadFavicon']);


//core system
Route::apiResource('branches', BranchController::class);
Route::apiResource('academic-years', AcademicYearController::class);
Route::apiResource('users', UserController::class);
//accademic
Route::apiResource('classes', ClassController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('class-teachers', ClassTeacherController::class);
Route::apiResource('class-subjects', ClassSubjectController::class);
Route::apiResource('class-rooms', ClassRoomController::class);
Route::apiResource('timetables', TimetableController::class);
//reception
Route::apiResource('admission-queries', AdmissionQueryController::class);
Route::prefix('front-office')->group(function () {
    Route::apiResource('complaints', ComplaintController::class);
    Route::get('postal-receives', [PostalController::class, 'getReceives']);
    Route::post('postal-receives', [PostalController::class, 'storeReceive']);
    Route::delete('postal-receives/{id}', [PostalController::class, 'deleteReceive']);
    Route::get('postal-dispatches', [PostalController::class, 'getDispatches']);
    Route::post('postal-dispatches', [PostalController::class, 'storeDispatch']);
    Route::delete('postal-dispatches/{id}', [PostalController::class, 'deleteDispatch']);
    Route::apiResource('phone-call-logs', PhoneCallLogController::class);
});
//student
Route::apiResource('student-categories', StudentCategoryController::class);
Route::post('students/bulk', [StudentController::class, 'bulkStore']);
Route::post('students/{id}', [StudentController::class, 'update']);
Route::apiResource('students', StudentController::class);
Route::apiResource('student-parents', StudentParentController::class);
Route::get('parent/children', [StudentParentController::class, 'myChildren']);
Route::apiResource('student-documents', StudentDocumentController::class);
Route::post('student-attendances/import', [StudentAttendanceController::class, 'import']);
Route::get('student-attendances/class-roster', [StudentAttendanceController::class, 'index']);
Route::apiResource('student-attendance', StudentAttendanceController::class);
Route::apiResource('student-attendances', StudentAttendanceController::class);
Route::apiResource('student-multi-classes', StudentMultiClassController::class);
//staff
Route::apiResource('staff-departments', StaffDepartmentController::class);
Route::post('staff/bulk', [StaffController::class, 'bulkStore']);
Route::apiResource('staff', StaffController::class);
Route::apiResource('staff-attendance', StaffAttendanceController::class);
Route::apiResource('staff-attendances', StaffAttendanceController::class);
//exam
Route::apiResource('exams', ExamController::class);
Route::apiResource('exam-schedules', ExamScheduleController::class);
Route::apiResource('exam-results', ExamResultController::class);
//fee
Route::apiResource('fee-types', FeeTypeController::class);
Route::apiResource('fee-groups', FeeGroupController::class);
Route::apiResource('fee-structures', FeeStructureController::class);
Route::apiResource('student-fees', StudentFeeController::class);
Route::apiResource('payments', PaymentController::class);
//certificate
Route::apiResource('certificate-templates', CertificateTemplateController::class);
Route::apiResource('generated-certificates', GeneratedCertificateController::class);
//study meterial
Route::apiResource('assignments', AssignmentController::class);
Route::apiResource('assignment-submissions', AssignmentSubmissionController::class);
Route::apiResource('study-materials', StudyMaterialController::class);
//download
Route::apiResource('download-content-types', DownloadContentTypeController::class);
Route::apiResource('download-contents', DownloadContentController::class);
Route::apiResource('shared-contents', SharedContentController::class);
Route::apiResource('videos', VideoController::class);
//communication
Route::apiResource('notifications', NotificationController::class);
Route::apiResource('messages', MessageController::class);
Route::apiResource('message-threads', MessageThreadController::class);
// utility
Route::apiResource('bulk-print-logs', BulkPrintLogController::class);
Route::apiResource('files', FileController::class);

// dashboard
Route::get('dashboard', [App\Http\Controllers\Api\DashboardController::class, 'index']);
Route::apiResource('todos', TodoController::class);
Route::apiResource('events', EventController::class);

    // Google Calendar OAuth & Sync
    Route::get('google/redirect', [GoogleCalendarController::class, 'redirectToGoogle']);
    Route::get('google/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);
    Route::get('google/status', [GoogleCalendarController::class, 'status']);
    Route::post('google/disconnect', [GoogleCalendarController::class, 'disconnect']);
});
