<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::first();
if ($user) {
    Laravel\Sanctum\Sanctum::actingAs($user, ['*']);
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::create(
        '/api/classes',
        'POST',
        [
            'name' => 'Grade 10',
            'academic_year_id' => 1,
            'branch_id' => 1,
            'sections' => ['Science', 'Humanities']
        ]
    )
);
echo "POST /api/classes Response:\n";
echo $response->getContent() . "\n\n";

$response2 = $kernel->handle(
    Illuminate\Http\Request::create('/api/classes', 'GET')
);
echo "GET /api/classes Response:\n";
echo $response2->getContent() . "\n";
