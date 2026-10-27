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
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\StudentIncidentController;
use App\Http\Controllers\Api\SidebarMenuController;
use App\Http\Controllers\Api\SaasController;
use App\Http\Controllers\Api\HomeworkController;
use App\Http\Controllers\Api\BehaviourSettingController;





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

// Password management APIs
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::match(['get', 'post'], '/password/verify-token', [AuthController::class, 'verifyResetToken']);
Route::match(['get', 'post'], '/verify-reset-token', [AuthController::class, 'verifyResetToken']);
Route::match(['get', 'post'], '/password/verify', [AuthController::class, 'verifyPassword']);
Route::match(['get', 'post'], '/verify-password', [AuthController::class, 'verifyPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::get('/reset-password', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Reset token received. Please submit a POST request to /api/reset-password with your new password.',
        'data' => [
            'email' => $request->query('email'),
            'token' => $request->query('token'),
        ]
    ]);
});
Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
Route::get('/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);
Route::get('/sidebar-menus', [SidebarMenuController::class, 'index']);

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
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::put('/general-settings', [GeneralSettingsController::class, 'update']);
        Route::post('/general-settings/logo', [GeneralSettingsController::class, 'uploadLogo']);
        Route::post('/general-settings/favicon', [GeneralSettingsController::class, 'uploadFavicon']);
    });


    //core system
    Route::apiResource('branches', BranchController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('users', UserController::class);
    // Roles & Permissions Management (Super Admin) - 100% Dynamic
    Route::get('roles/{id}/permissions', [RoleController::class, 'getRolePermissions']);
    Route::post('roles/{id}/permissions', [RoleController::class, 'syncRolePermissions']);
    Route::get('permissions', [RoleController::class, 'getPermissions']);
    Route::post('permissions', [RoleController::class, 'storePermission']);
    Route::delete('permissions/{id}', [RoleController::class, 'destroyPermission']);
    Route::apiResource('roles', RoleController::class);
    Route::get('role', [RoleController::class, 'index']); // Alias for singular /api/role
    Route::get('users/{id}/permissions', [RoleController::class, 'getUserPermissions']);
    Route::post('users/{id}/roles', [RoleController::class, 'assignRoleToUser']);
    Route::post('users/{id}/permissions', [RoleController::class, 'assignPermissionToUser']);
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

    // Add Additional Contact APIs (From Sibling & From Staff)
    Route::get('students/additional-contact/siblings', [StudentController::class, 'getSiblings']);
    Route::get('students/additional-contact/sibling/{id}', [StudentController::class, 'getSiblingContactInfo']);
    Route::get('students/additional-contact/staff', [StudentController::class, 'getStaffDropdown']);
    Route::get('students/additional-contact/staff/{id}', [StudentController::class, 'getStaffContactInfo']);
    Route::post('students/additional-contact', [StudentController::class, 'resolveAdditionalContact']);
    Route::get('students/siblings', [StudentController::class, 'getSiblings']);
    Route::get('students/{id}/sibling-info', [StudentController::class, 'getSiblingContactInfo']);
    Route::get('staff/{id}/contact-info', [StudentController::class, 'getStaffContactInfo']);

    Route::post('students/bulk', [StudentController::class, 'bulkStore']);
    Route::post('students/{id}', [StudentController::class, 'update']);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('student-parents', StudentParentController::class);
    Route::get('parent/children', [StudentParentController::class, 'myChildren']);
    Route::apiResource('student-documents', StudentDocumentController::class);
    Route::post('student-attendances/import', [StudentAttendanceController::class, 'import']);
    Route::get('student-attendances/class-roster', [StudentAttendanceController::class, 'index']);
    Route::get('student-attendances/monthly-report', [StudentAttendanceController::class, 'monthlyReport']);
    Route::get('student-attendance-report', [StudentAttendanceController::class, 'monthlyReport']);
    Route::get('students/{student_id}/attendance-report', [StudentAttendanceController::class, 'monthlyReport']);
    Route::apiResource('student-attendance', StudentAttendanceController::class);
    Route::apiResource('student-attendances', StudentAttendanceController::class);
    Route::apiResource('student-multi-classes', StudentMultiClassController::class);

    // Home Work Module (STUDENT -> Home Work)
    Route::get('homework-report', [HomeworkController::class, 'report']);
    Route::get('homework-report/evaluation/{submissionId}', [HomeworkController::class, 'evaluationSummary']);
    Route::post('homeworks/{id}/evaluate', [HomeworkController::class, 'evaluate']);
    Route::post('homeworks/{id}', [HomeworkController::class, 'update']);
    Route::apiResource('homeworks', HomeworkController::class);
    Route::get('homework-list', [HomeworkController::class, 'index']);
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
    Route::get('google/status', [GoogleCalendarController::class, 'status']);
    Route::post('google/disconnect', [GoogleCalendarController::class, 'disconnect']);

    // Student Behaviour Incidents & Remarks Master & Assignment
    Route::apiResource('incidents', IncidentController::class);
    Route::get('students/{student_id}/incidents', [StudentIncidentController::class, 'getByStudent']);
    Route::get('students/{student_id}/behaviour-records', [StudentIncidentController::class, 'getByStudent']);
    Route::post('students/{student_id}/incidents', [StudentIncidentController::class, 'assignToStudent']);
    Route::apiResource('student-incidents', StudentIncidentController::class);
    Route::apiResource('behaviour-records', StudentIncidentController::class);

    // Behaviour Reports (Screenshots 3, 4, 5)
    // 1. Remarks Report / Incident Wise Report (Filters: class, section, incident_id)
    Route::get('reports/remarks-report', [StudentIncidentController::class, 'remarksReport']);
    Route::get('reports/incident-wise', [StudentIncidentController::class, 'remarksReport']);
    Route::get('remarks-report', [StudentIncidentController::class, 'remarksReport']);
    Route::get('incident-wise-report', [StudentIncidentController::class, 'remarksReport']);

    // 2. Student Incident Report (Table & Details Modal)
    Route::get('reports/student-incidents', [StudentIncidentController::class, 'studentIncidentReport']);
    Route::get('reports/student-incidents/{student_id}', [StudentIncidentController::class, 'studentIncidentReportDetails']);
    Route::get('student-incident-reports', [StudentIncidentController::class, 'studentIncidentReport']);

    // 3. Class Section Wise Rank Report & Details
    Route::get('reports/class-section-rank', [StudentIncidentController::class, 'classSectionRankReport']);
    Route::get('reports/class-section-rank/{class_id}', [StudentIncidentController::class, 'classSectionRankDetails']);
    Route::get('class-section-rank-report', [StudentIncidentController::class, 'classSectionRankReport']);

    // 4. Behaviour Settings (Incident Comment & View Option Toggles)
    Route::get('behaviour-settings', [BehaviourSettingController::class, 'show']);
    Route::post('behaviour-settings', [BehaviourSettingController::class, 'update']);

    // Sidebar Menus Management
    Route::get('sidebar-menus/items', [SidebarMenuController::class, 'items']);
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::post('sidebar-menus', [SidebarMenuController::class, 'store']);
        Route::put('sidebar-menus/{id}', [SidebarMenuController::class, 'update']);
        Route::patch('sidebar-menus/{id}/sub-menu-status', [SidebarMenuController::class, 'toggleSubMenuStatus']);
        Route::patch('sidebar-sub-menus/{id}/status', [SidebarMenuController::class, 'updateSubMenuStatus']);
        Route::delete('sidebar-menus/{id}', [SidebarMenuController::class, 'destroy']);
    });

    // Super Admin Management
    Route::middleware('role:super-admin')->group(function () {
        Route::get('super-admins', [SaasController::class, 'getSuperAdmins']);
        Route::post('super-admin', [SaasController::class, 'createSuperAdmin']);

        // Legacy / Alternative Aliases
        Route::get('saas/super-admins', [SaasController::class, 'getSuperAdmins']);
        Route::post('saas/super-admin', [SaasController::class, 'createSuperAdmin']);
        Route::get('saas/dashboard', [SaasController::class, 'dashboard']);
        Route::get('saas/schools', [SaasController::class, 'getSchools']);
        Route::post('saas/schools', [SaasController::class, 'createSchool']);
        Route::put('saas/schools/{id}/status', [SaasController::class, 'updateSchoolStatus']);
    });

});
