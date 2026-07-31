<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

$user = User::first(); 
Sanctum::actingAs($user);

// Get or create dependencies
$academicYear = AcademicYear::firstOrCreate(['name' => '2026-2027'], ['start_date' => '2026-04-01', 'end_date' => '2027-03-31']);
$class = Classes::firstOrCreate(['name' => 'Class X', 'branch_id' => 1, 'academic_year_id' => $academicYear->id]);
$section = Section::firstOrCreate(['name' => 'Section A', 'class_id' => $class->id]);

// Let's create a student
echo "Testing Student Create Endpoint...\n";
$request = Request::create('/api/students', 'POST', [
    'admission_no' => 'STU-' . time(),
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john_' . time() . '@example.com',
    'class_id' => $class->id,
    'section_id' => $section->id,
    'academic_year_id' => $academicYear->id,
    'date_of_birth' => '2010-05-10',
    'gender' => 'male',
    'phone' => '1234567890',
    'nationality' => 'American',
    'current_address' => '123 Street Ave',
    'permanent_address' => '456 Block Rd'
]);
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";

if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
    echo "\nVerification Successful: Student created.\n";
    $studentData = json_decode($response->getContent(), true)['data'];
    $studentId = $studentData['id'];

    echo "\nTesting Student Show Endpoint...\n";
    $requestShow = Request::create('/api/students/' . $studentId, 'GET');
    $responseShow = app()->handle($requestShow);
    echo "Show Response: " . $responseShow->getContent() . "\n";
} else {
    $err = json_decode($response->getContent(), true);
    echo "\nVerification Failed: " . ($err['message'] ?? 'Unknown error') . "\n";
}
