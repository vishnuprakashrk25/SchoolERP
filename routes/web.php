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
    $frontendUrl = env('FRONTEND_URL', 'https://yesmlr.yesindiaschools.com');
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

// Fallback route to serve /storage/... files dynamically if symlinks are disabled by host
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');
