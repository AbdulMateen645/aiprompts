<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PromptController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserPromptController;

// Public Routes with Rate Limiting
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/prompts', [PromptController::class, 'index']);
    Route::get('/prompts/featured', [PromptController::class, 'featured']);
    Route::get('/prompts/category/{slug}', [PromptController::class, 'byCategory']);
    Route::get('/prompts/search', [PromptController::class, 'search']);
    Route::get('/prompts/{slug}', [PromptController::class, 'show']);
    Route::post('/prompts/{id}/views', [PromptController::class, 'incrementViews']);
    Route::post('/prompts/{id}/like', [PromptController::class, 'toggleLike']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/auth/google', [AuthController::class, 'googleLogin']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/user/prompts/stats', [UserPromptController::class, 'stats']);
    Route::get('/user/prompts', [UserPromptController::class, 'index']);
    Route::post('/user/prompts', [UserPromptController::class, 'store']);
});
