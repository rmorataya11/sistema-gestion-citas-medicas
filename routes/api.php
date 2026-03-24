<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/appointments', [AppointmentController::class, 'store']);
});