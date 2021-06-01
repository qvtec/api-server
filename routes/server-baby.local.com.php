<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\Baby\DashboardController;

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
Route::get('user', [CurrentUserController::class, 'index']);

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::delete('', [CurrentUserController::class, 'destroy']);
    Route::delete('profile-photo', [CurrentUserController::class, 'photoDestroy']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('dashboard', [DashboardController::class, 'index']);
});
