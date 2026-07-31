<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Student;
use App\Models\User;

$user = User::first() ?? User::factory()->create();
$token = $user->createToken('test')->plainTextToken;

function makeRequest($method, $uri, $data = [], $files = []) {
    global $kernel, $token;
    $server = [
        'CONTENT_TYPE' => empty($files) ? 'application/json' : 'multipart/form-data',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
    ];

    $content = empty($files) ? json_encode($data) : '';

    $request = Request::create($uri, $method, empty($files) ? [] : $data, [], $files, $server, $content);
    $response = $kernel->handle($request);
    
    echo "[$method] $uri  --> Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() >= 400) {
        echo "Response Error: " . $response->getContent() . "\n";
    }
    return json_decode($response->getContent(), true);
}

// Ensure at least one student exists
$student = Student::first();
if (!$student) {
    $student = Student::create([
        'user_id' => 1,
        'admission_no' => 'ADM-TEST-X',
        'roll_no' => '101'
    ]);
}

echo "Testing StudentCategory...\n";
$catRes = makeRequest('POST', '/api/student-categories', [
    'name' => 'API Test Category',
    'description' => 'Created via test script'
]);
if (isset($catRes['data']['id'])) {
    makeRequest('DELETE', '/api/student-categories/' . $catRes['data']['id']);
}

echo "\nTesting StudentParent...\n";
$parRes = makeRequest('POST', '/api/student-parents', [
    'student_id' => $student->id,
    'father_name' => 'John Doe',
    'father_phone' => '1234567890'
]);
if (isset($parRes['data']['id'])) {
    makeRequest('DELETE', '/api/student-parents/' . $parRes['data']['id']);
}

echo "\nTesting StudentDocument...\n";
// Create a fake file for upload
$testFile = sys_get_temp_dir() . '/test_doc.jpg';
file_put_contents($testFile, 'dummy content');
$uploadedFile = new UploadedFile($testFile, 'test_doc.jpg', 'image/jpeg', null, true);

$docRes = makeRequest('POST', '/api/student-documents', [
    'student_id' => $student->id,
    'title' => 'Birth Certificate'
], [
    'file' => $uploadedFile
]);
if (isset($docRes['data']['id'])) {
    makeRequest('DELETE', '/api/student-documents/' . $docRes['data']['id']);
}
@unlink($testFile);

echo "\nAll Tests executed.\n";
