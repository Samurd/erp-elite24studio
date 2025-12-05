<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para broadcasting/autenticación de Reverb
Route::middleware(['web'])->group(function () {
    Broadcast::routes();
});

Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
Route::apiResource('meetings', \App\Http\Controllers\Api\MeetingController::class)->names('api.meetings');
Route::apiResource('areas', \App\Http\Controllers\Api\AreaController::class)->only(['index', 'show']);
