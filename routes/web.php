<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Add login route for Laravel's auth system
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Admin Authentication (No middleware)
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');

// Password Reset Routes
Route::get('/admin/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('admin.password.request');
Route::post('/admin/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('admin.password.email');
Route::get('/admin/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('admin.password.reset');
Route::post('/admin/reset-password', [PasswordResetController::class, 'resetPassword'])->name('admin.password.update');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::post('/auth/logout', [GoogleAuthController::class, 'logout'])->name('user.logout');

// Protected Admin Routes
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Prompts Routes
    Route::get('/prompts', [AdminController::class, 'prompts'])->name('prompts.index');
    Route::get('/prompts/pending', [AdminController::class, 'pendingPrompts'])->name('prompts.pending');
    Route::post('/prompts/{id}/approve', [AdminController::class, 'approvePrompt'])->name('prompts.approve');
    Route::post('/prompts/{id}/reject', [AdminController::class, 'rejectPrompt'])->name('prompts.reject');
    Route::get('/prompts/create', [AdminController::class, 'createPrompt'])->name('prompts.create');
    Route::post('/prompts', [AdminController::class, 'storePrompt'])->name('prompts.store');
    Route::get('/prompts/{id}/edit', [AdminController::class, 'editPrompt'])->name('prompts.edit');
    Route::put('/prompts/{id}', [AdminController::class, 'updatePrompt'])->name('prompts.update');
    Route::delete('/prompts/{id}', [AdminController::class, 'deletePrompt'])->name('prompts.delete');
    
    // Blogs Routes
    Route::get('/blogs', [AdminController::class, 'blogs'])->name('blogs.index');
    Route::get('/blogs/create', [AdminController::class, 'createBlog'])->name('blogs.create');
    Route::post('/blogs', [AdminController::class, 'storeBlog'])->name('blogs.store');
    Route::get('/blogs/{id}/edit', [AdminController::class, 'editBlog'])->name('blogs.edit');
    Route::put('/blogs/{id}', [AdminController::class, 'updateBlog'])->name('blogs.update');
    Route::delete('/blogs/{id}', [AdminController::class, 'deleteBlog'])->name('blogs.delete');
    
    // Categories Routes
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    
    // Contacts Routes
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('contacts.index');
    Route::get('/contacts/{id}', [AdminController::class, 'showContact'])->name('contacts.show');
    Route::delete('/contacts/{id}', [AdminController::class, 'deleteContact'])->name('contacts.delete');
    
    // Users Routes
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
});
