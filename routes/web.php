<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatQaController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\BlogSuggestionController;
use Illuminate\Support\Facades\Route;

// Homepage pubblica
Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Area protetta
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Gestione utenti (solo superuser e admin)
    Route::middleware('role:superuser,admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'destroy']);

        // Gestione Chat Q&A
        Route::get('/chat-qa', [ChatQaController::class, 'index'])->name('chat-qa.index');
        Route::get('/chat-qa/section/{section}/edit', [ChatQaController::class, 'editSection'])->name('chat-qa.edit-section');
        Route::put('/chat-qa/section/{section}', [ChatQaController::class, 'updateSection'])->name('chat-qa.update-section');
        Route::get('/chat-qa/section/{section}/qa/create', [ChatQaController::class, 'createQa'])->name('chat-qa.create-qa');
        Route::post('/chat-qa/section/{section}/qa', [ChatQaController::class, 'storeQa'])->name('chat-qa.store-qa');
        Route::get('/chat-qa/qa/{qa}/edit', [ChatQaController::class, 'editQa'])->name('chat-qa.edit-qa');
        Route::put('/chat-qa/qa/{qa}', [ChatQaController::class, 'updateQa'])->name('chat-qa.update-qa');
        Route::delete('/chat-qa/qa/{qa}', [ChatQaController::class, 'destroyQa'])->name('chat-qa.destroy-qa');

        // Blog Suggestions (Scraper) - must be before {post} wildcard
        Route::get('/blog/suggestions', [BlogSuggestionController::class, 'index'])->name('blog.suggestions');
        Route::post('/blog/suggestions/generate', [BlogSuggestionController::class, 'generate'])->name('blog.suggestions.generate');
        Route::get('/blog/suggestions/status', [BlogSuggestionController::class, 'status'])->name('blog.suggestions.status');
        Route::post('/blog/suggestions/{suggestion}/approve', [BlogSuggestionController::class, 'approve'])->name('blog.suggestions.approve');
        Route::post('/blog/suggestions/{suggestion}/reject', [BlogSuggestionController::class, 'reject'])->name('blog.suggestions.reject');
        Route::post('/blog/suggestions/bulk', [BlogSuggestionController::class, 'bulk'])->name('blog.suggestions.bulk');

        // Gestione Blog
        Route::get('/blog', [BlogPostController::class, 'index'])->name('blog.index');
        Route::get('/blog/create', [BlogPostController::class, 'create'])->name('blog.create');
        Route::post('/blog', [BlogPostController::class, 'store'])->name('blog.store');
        Route::get('/blog/{post}/edit', [BlogPostController::class, 'edit'])->name('blog.edit');
        Route::put('/blog/{post}', [BlogPostController::class, 'update'])->name('blog.update');
        Route::delete('/blog/{post}', [BlogPostController::class, 'destroy'])->name('blog.destroy');
        Route::post('/blog/bulk', [BlogPostController::class, 'bulk'])->name('blog.bulk');
    });
});
