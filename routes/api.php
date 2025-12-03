<?php

use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Route;


Route::middleware(['apicheck'])->controller(APIController::class)->group(function () {
    Route::get('/test', 'testing');
});
