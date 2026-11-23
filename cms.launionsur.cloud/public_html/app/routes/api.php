<?php

use App\Http\Controllers\BannerApiController;
use App\Http\Controllers\CmsBlockApiController;
use Illuminate\Support\Facades\Route;

// Public API routes - No authentication required

Route::get('/banners/{page}', [BannerApiController::class, 'getByPage']);

Route::get('/cms-blocks/{page}', [CmsBlockApiController::class, 'getByPage']);
Route::get('/cms-blocks/slug/{slug}', [CmsBlockApiController::class, 'getBySlug']);

// Services public API
use App\Http\Controllers\ServiceApiController;
use App\Http\Controllers\MediaApiController;

Route::get('/services/categories', [ServiceApiController::class, 'categories']);
Route::get('/services/categories/{slug}', [ServiceApiController::class, 'category']);
Route::get('/services/slug/{slug}', [ServiceApiController::class, 'show']);
Route::get('/services/search', [ServiceApiController::class, 'search']);

// Media public API - read-only
// Note: Specific routes must come before parameterized routes
Route::get('/media', [MediaApiController::class, 'index']);
Route::get('/media/search', [MediaApiController::class, 'search']);
Route::get('/media/tags', [MediaApiController::class, 'tags']);
Route::get('/media/collections', [MediaApiController::class, 'collections']);
Route::get('/media/{id}/usage', [MediaApiController::class, 'usage']);
Route::get('/media/{id}', [MediaApiController::class, 'show']);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\UserController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::get('/auth/me', [AuthController::class, 'me'])->middleware(\App\Http\Middleware\ApiTokenAuth::class);

// Password reset (API)
Route::post('/auth/password/forgot', [PasswordResetController::class, 'request']);
Route::post('/auth/password/reset', [PasswordResetController::class, 'confirm']);

// User management (API) - require api token auth
Route::middleware(\App\Http\Middleware\ApiTokenAuth::class)->prefix('users')->group(function () {
    // List users
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::put('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
    Route::post('{id}/change-password', [UserController::class, 'changePassword']);
    Route::post('{id}/assign-role', [UserController::class, 'assignRole']);
});

// Audit logs API - require api token auth
Route::middleware(\App\Http\Middleware\ApiTokenAuth::class)->prefix('audit-logs')->group(function () {
    // List audit logs (Editor+)
    Route::get('/', [AuditLogController::class, 'index']);
    // Get specific audit log (Editor+)
    Route::get('{auditLog}', [AuditLogController::class, 'show']);
    // Restore audit log (Super Admin only) - enforce policy at route level too
    Route::post('{auditLog}/restore', [AuditLogController::class, 'restore'])->middleware('can:restore,auditLog');
});
