<?php

use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\ProductFeatureController;
use App\Http\Controllers\Api\Admin\ProductScreenshotController;
use App\Http\Controllers\Api\Admin\SiteContentController as AdminSiteContentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Public\ProductController as PublicProductController;
use App\Http\Controllers\Api\Public\SiteContentController as PublicSiteContentController;
use Illuminate\Support\Facades\Route;

// ── CMS: Public (no auth) — consumed by the marketing website ──────────────
Route::prefix('v1/public')->group(function () {
    Route::get('products', [PublicProductController::class, 'index']);
    Route::get('products/{slug}', [PublicProductController::class, 'show']);
    Route::get('site-content/{key}', [PublicSiteContentController::class, 'show']);
});

// ── CMS: Auth ────────────────────────────────────────────────────────────
Route::post('v1/auth/login', [AuthController::class, 'login']);

// ── CMS: Admin (Sanctum token required) ─────────────────────────────────────
Route::prefix('v1/admin')->middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    Route::apiResource('products', AdminProductController::class);

    Route::post('products/{product}/features', [ProductFeatureController::class, 'store']);
    Route::put('features/{feature}', [ProductFeatureController::class, 'update']);
    Route::delete('features/{feature}', [ProductFeatureController::class, 'destroy']);

    Route::post('products/{product}/screenshots', [ProductScreenshotController::class, 'store']);
    Route::put('screenshots/{screenshot}', [ProductScreenshotController::class, 'update']);
    Route::delete('screenshots/{screenshot}', [ProductScreenshotController::class, 'destroy']);

    Route::get('site-content', [AdminSiteContentController::class, 'index']);
    Route::get('site-content/{key}', [AdminSiteContentController::class, 'show']);
    Route::put('site-content/{key}', [AdminSiteContentController::class, 'update']);

    Route::get('media', [MediaController::class, 'index']);
    Route::post('media', [MediaController::class, 'store']);
    Route::delete('media/{media}', [MediaController::class, 'destroy']);
});
