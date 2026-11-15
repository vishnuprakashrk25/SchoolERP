<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Redirect /reset-password from backend URL directly to the frontend SPA
Route::get('/reset-password', function (\Illuminate\Http\Request $request) {
    $frontendUrl = config('app.frontend_url') ?: env('FRONTEND_URL') ?: 'https://app.cyberduce.com';
    if (str_contains($frontendUrl, ',')) {
        $frontendUrl = trim(explode(',', $frontendUrl)[0]);
    }
    $query = $request->getQueryString();
    $target = rtrim($frontendUrl, '/') . '/reset-password' . ($query ? '?' . $query : '');
    return redirect()->away($target);
});

Route::get('/storage-link', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (file_exists($link)) {
        return response()->json(['success' => true, 'message' => 'The [public/storage] link already exists.']);
    }

    // Try 1: Artisan call
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        if (file_exists($link)) {
            return response()->json(['success' => true, 'message' => 'Storage link created successfully via Artisan.']);
        }
    } catch (\Throwable $e) {}

    // Try 2: PHP built-in symlink
    try {
        if (function_exists('symlink')) {
            @symlink($target, $link);
            if (file_exists($link)) {
                return response()->json(['success' => true, 'message' => 'Storage link created successfully via PHP symlink.']);
            }
        }
    } catch (\Throwable $e) {}

    // Try 3: Executive command (Linux ln -s or Windows mklink /J)
    if (function_exists('exec')) {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                @exec("cmd /c mklink /J \"{$link}\" \"{$target}\"");
            } else {
                @exec("ln -s \"{$target}\" \"{$link}\"");
            }
            if (file_exists($link)) {
                return response()->json(['success' => true, 'message' => 'Storage link created successfully via system command.']);
            }
        } catch (\Throwable $e) {}
    }

    return response()->json([
        'success' => false,
        'error' => 'Symlinks are disabled by your web host. Please use direct public upload.'
    ], 500);
});

// Clear Laravel optimize/route/config/view cache via URL on shared hosting
Route::get('/clear-cache', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        if (class_exists(\App\Models\SidebarMenu::class) && method_exists(\App\Models\SidebarMenu::class, 'clearMenuCache')) {
            \App\Models\SidebarMenu::clearMenuCache();
        }
        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully (routes, config, views, application & sidebar menu cache flushed).'
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Generate Swagger API documentation via URL on shared hosting
Route::get('/generate-docs', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('l5-swagger:generate');
        return response()->json([
            'success' => true,
            'message' => 'L5-Swagger documentation regenerated successfully.'
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

// Fallback route to serve /storage/... files dynamically if symlinks are disabled by host
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');
