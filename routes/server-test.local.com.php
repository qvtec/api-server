<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\Server2\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['auth:sanctum'])->prefix('user')->group(function(){
    Route::post('', [CurrentUserController::class, 'user']);
    Route::delete('', [CurrentUserController::class, 'destroy']);
    Route::delete('profile-photo', [CurrentUserController::class, 'photoDestroy']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('dashboard', [DashboardController::class, 'index']);
});