<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('api')
    ->prefix('v1') // 'api/v1' is for service providers.
    ->group(base_path('routes/api_v1.php'));


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
