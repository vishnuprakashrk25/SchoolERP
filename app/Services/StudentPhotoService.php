<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class StudentPhotoService
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];

    private const BASE64_EXT_MAP = [
        'jpeg' => 'jpg',
        'jpg' => 'jpg',
        'png' => 'png',
        'webp' => 'webp',
        'gif' => 'gif',
        'svg+xml' => 'svg',
    ];

    /**
     * Process a photo upload from multipart UploadedFile, base64 data URI, or safe relative path.
     */
    public static function upload($request, string $key, string $targetDir, string $prefix = ''): ?string
    {
        $dir = public_path($targetDir);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($request instanceof Request && $request->hasFile($key)) {
            return self::storeUploadedFile($request->file($key), $dir, $targetDir, $prefix);
        }

        $value = $request instanceof Request
            ? $request->input($key)
            : (is_array($request) ? ($request[$key] ?? null) : null);

        if ($value instanceof UploadedFile) {
            return self::storeUploadedFile($value, $dir, $targetDir, $prefix);
        }

        if (is_string($value) && str_starts_with($value, 'data:image/')) {
            return self::storeBase64Image($value, $dir, $targetDir, $prefix);
        }

        if (is_string($value) && !empty($value) && strlen($value) <= 255) {
            $cleanValue = trim($value);
            if (!str_contains($cleanValue, '..') && !str_ends_with(strtolower($cleanValue), '.php')) {
                return $cleanValue;
            }
        }

        return null;
    }

    private static function storeUploadedFile(UploadedFile $file, string $dir, string $targetDir, string $prefix): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            $ext = 'jpg';
        }

        $filename = self::buildFilename($prefix, $ext);
        $file->move($dir, $filename);

        return rtrim($targetDir, '/') . '/' . $filename;
    }

    private static function storeBase64Image(string $value, string $dir, string $targetDir, string $prefix): ?string
    {
        preg_match('/data:image\/([a-zA-Z0-9\+]+);base64,(.*)/', $value, $matches);
        if (empty($matches[2])) {
            return null;
        }

        $rawExt = strtolower($matches[1] ?? 'png');
        $extension = self::BASE64_EXT_MAP[$rawExt] ?? 'png';

        $decodedData = base64_decode($matches[2], true);
        if ($decodedData === false || strlen($decodedData) === 0) {
            return null;
        }

        $filename = self::buildFilename($prefix, $extension);
        file_put_contents($dir . '/' . $filename, $decodedData);

        return rtrim($targetDir, '/') . '/' . $filename;
    }

    private static function buildFilename(string $prefix, string $extension): string
    {
        return time() . '_' . ($prefix ? $prefix . '_' : '') . uniqid() . '.' . $extension;
    }
}

