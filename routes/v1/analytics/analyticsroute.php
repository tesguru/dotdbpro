<?php

use App\Http\Controllers\api\v1\analytics\AnalyticsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['access_token'])->controller(AnalyticsController::class)->group(function () {
    Route::get('/test', 'testing');
    Route::get('/get-data-limit', 'getDataLimit');
    Route::post('/get-related-keywords', 'getRelatedKeywords');
    Route::get('/get-ai-meaning', 'getAiMeaning');

});


