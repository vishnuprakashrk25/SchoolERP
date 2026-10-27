<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="ERP School API Documentation",
 *      description="Swagger OpenApi description for ERP School Project",
 *      @OA\Contact(
 *          email="admin@erpschool.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url="/",
 *      description="Current Host (Auto-detects localhost or live domain)"
 * )
 * @OA\Server(
 *      url="http://127.0.0.1:8000",
 *      description="Local Artisan Server (127.0.0.1:8000)"
 * )
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Local Development Server (localhost:8000)"
 * )
 * @OA\Server(
 *      url="https://dev.cyberduce.com",
 *      description="Live Server (Hostinger)"
 * )
 * @OA\Server(
 *      url="https://yesmlr.yesindiaschools.com",
 *      description="Yes India Schools Live Server"
 * )
 * @OA\Server(
 *      url="http://localhost/erp-school/public",
 *      description="Local XAMPP Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use Bearer token for API clients and mobile apps. Login via /api/login to get the token.",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum",
 * )
 *
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     description="Cookie based auth for SPA. First call GET /sanctum/csrf-cookie, then POST /api/login.",
 *     name="XSRF-TOKEN",
 *     in="cookie",
 *     securityScheme="cookie_auth",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
