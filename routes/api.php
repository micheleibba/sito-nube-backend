<?php

use App\Http\Controllers\Api\ChatController;
use App\Models\BlogPost;
use Illuminate\Support\Facades\Route;

Route::get('/chat/sections', [ChatController::class, 'sections']);

// TEMPORARY: Reset admin password - DELETE AFTER USE
Route::get('/reset-admin-pwd', function () {
    $user = \App\Models\User::where('email', 'admin@nube.it')->first();
    if (!$user) return response()->json(['error' => 'User not found']);
    $user->password = \Illuminate\Support\Facades\Hash::make('password');
    $user->save();
    return response()->json(['ok' => true, 'hash_preview' => substr($user->password, 0, 20) . '...']);
});
Route::post('/chat/message', [ChatController::class, 'chat'])->middleware('chat.ratelimit');

// Blog API
Route::get('/blog/latest', function () {
    $posts = BlogPost::published()
        ->orderByDesc('published_at')
        ->take(5)
        ->get()
        ->map(fn($post) => [
            'id' => $post->id,
            'title_en' => $post->title_en,
            'title_it' => $post->title_it,
            'slug_en' => $post->slug_en,
            'slug_it' => $post->slug_it,
            'cover_image' => $post->cover_image ? asset('storage/' . $post->cover_image) : null,
            'published_at' => $post->published_at->format('Y-m-d'),
        ]);

    return response()->json($posts);
});

Route::get('/blog/{slug}', function (string $slug) {
    $post = BlogPost::published()
        ->where('slug_en', $slug)
        ->orWhere('slug_it', $slug)
        ->first();

    if (!$post) {
        return response()->json(['error' => 'Post not found'], 404);
    }

    return response()->json([
        'id' => $post->id,
        'title_en' => $post->title_en,
        'title_it' => $post->title_it,
        'slug_en' => $post->slug_en,
        'slug_it' => $post->slug_it,
        'text_en' => $post->text_en,
        'text_it' => $post->text_it,
        'cover_image' => $post->cover_image ? asset('storage/' . $post->cover_image) : null,
        'meta_description_en' => $post->meta_description_en,
        'meta_description_it' => $post->meta_description_it,
        'published_at' => $post->published_at->format('Y-m-d'),
    ]);
});
