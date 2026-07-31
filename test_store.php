<?php
$req = new \Illuminate\Http\Request();
$req->merge([
    'admission_no'=>uniqid(),
    'first_name'=>'test',
    'email'=>uniqid().'@gmail.com',
    'class_id'=>1,
    'section_id'=>2,
    'academic_year_id'=>1
]);
try {
    app('App\Http\Controllers\Api\StudentController')->store($req);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . "\n";
    echo "LINE: " . $e->getLine() . "\n";
}
