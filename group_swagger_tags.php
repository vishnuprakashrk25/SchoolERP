<?php

$dir = __DIR__ . '/app/Http/Controllers/Api';
$files = glob($dir . '/*.php');

$moduleMapping = [
    // Phase 0: Authentication
    'AuthController' => '0. Authentication',
    
    // Phase 1: Core System
    'BranchController' => '1. Core System',
    'AcademicYearController' => '1. Core System',
    'UserController' => '1. Core System',
    
    // Phase 2: Staff / HR
    'StaffDepartmentController' => '2. Staff & HR',
    'StaffController' => '2. Staff & HR',
    'StaffAttendanceController' => '2. Staff & HR',
    
    // Phase 3: Academics
    'SubjectController' => '3. Academic Infrastructure',
    'ClassController' => '3. Academic Infrastructure',
    'SectionController' => '3. Academic Infrastructure',
    'ClassSubjectController' => '3. Academic Infrastructure',
    'TimetableController' => '3. Academic Infrastructure',
    
    // Phase 4: Student Info
    'StudentCategoryController' => '4. Student Enrollment',
    'StudentParentController' => '4. Student Enrollment',
    'StudentController' => '4. Student Enrollment',
    'StudentDocumentController' => '4. Student Enrollment',
    'StudentMultiClassController' => '4. Student Enrollment',
    'StudentAttendanceController' => '4. Student Enrollment',
    
    // Phase 5: Finance & Fees
    'FeeTypeController' => '5. Finance & Fee Collection',
    'FeeGroupController' => '5. Finance & Fee Collection',
    'FeeStructureController' => '5. Finance & Fee Collection',
    'StudentFeeController' => '5. Finance & Fee Collection',
    'PaymentController' => '5. Finance & Fee Collection',
    
    // Phase 6: Examinations
    'ExamController' => '6. Examinations & Assessment',
    'ExamScheduleController' => '6. Examinations & Assessment',
    'ExamResultController' => '6. Examinations & Assessment',
    
    // Phase 7: E-Learning & Content
    'AssignmentController' => '7. E-Learning & Content',
    'AssignmentSubmissionController' => '7. E-Learning & Content',
    'StudyMaterialController' => '7. E-Learning & Content',
    'DownloadContentTypeController' => '7. E-Learning & Content',
    'DownloadContentController' => '7. E-Learning & Content',
    'SharedContentController' => '7. E-Learning & Content',
    'VideoController' => '7. E-Learning & Content',
    
    // Phase 8: Communication & Utilities
    'NotificationController' => '8. Communication & Utils',
    'MessageController' => '8. Communication & Utils',
    'MessageThreadController' => '8. Communication & Utils',
    'CertificateTemplateController' => '8. Communication & Utils',
    'GeneratedCertificateController' => '8. Communication & Utils',
    'BulkPrintLogController' => '8. Communication & Utils',
    'FileController' => '8. Communication & Utils',
];

$successCount = 0;

foreach ($files as $file) {
    if (is_dir($file)) continue;
    $basename = basename($file, '.php');

    if (!isset($moduleMapping[$basename])) {
        continue;
    }

    $componentName = str_replace('Controller', '', $basename);
    $newTag = $moduleMapping[$basename] . ' - ' . $componentName;
    $content = file_get_contents($file);

    // Replace tags={"*"} with tags={"ModuleName"}
    $newContent = preg_replace('/tags=\{"[^"]+"}/', 'tags={"' . $newTag . '"}', $content);

    if ($newContent !== $content) {
        file_put_contents($file, $newContent);
        $successCount++;
        echo "Updated tags for $basename to '$newTag'\n";
    }
}

echo "Successfully updated Swagger tags for $successCount controllers.\n";
