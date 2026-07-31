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
 *      url="https://dev.cyberduce.com",
 *      description="Live Server"
 * )
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Local Development Server (Artisan serve)"
 * )
 * @OA\Server(
 *      url="http://localhost/erp-school/public",
 *      description="Local XAMPP Server"
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Configured Server Host (from .env)"
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
