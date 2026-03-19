<?php

use App\Http\Controllers\Api\LocationUpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/location/{followedPerson}', [LocationUpdateController::class, 'store'])
    ->name('api.location.store');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
