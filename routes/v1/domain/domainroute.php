<?php

use App\Http\Controllers\api\v1\blog\BlogController;
use Illuminate\Support\Facades\Route;


Route::middleware(['api.key'])->controller(BlogController::class)->group(function () {
  Route::get('/posts', [BlogPostController::class, 'index']);
        Route::get('/posts/featured', [BlogPostController::class, 'featured']);
        Route::get('/posts/{slug}', [BlogPostController::class, 'show']);
});
