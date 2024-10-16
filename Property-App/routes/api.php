<?php

use App\Http\Controllers\Constants\GovernorateController;
use App\Http\Controllers\Constants\PropertyStateController;
use App\Http\Controllers\Constants\RegionController;
use App\Http\Controllers\Constants\ReservationStateController;
use App\Http\Controllers\Constants\ReservationTypeController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\UserControllers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

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

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::group([
        'middleware' => ['auth_user:api'],
    ], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [UserControllers::class, 'me']);
        Route::post('updateUser', [UserControllers::class, 'updateUser']);
        Route::post('changePassword', [UserControllers::class, 'changePassword']);
        Route::post('/', [ReportController::class, 'store']);
        Route::post('/', [PropertyController::class, 'store']);

    });
});
Route::group([
    'prefix' => 'reports',
    'middleware' => ['auth_user:api',]
], function () {
    Route::post('/', [ReportController::class, 'store']);
    Route::get('/', [ReportController::class, 'index']);
    Route::post('/{id}', [ReportController::class, 'show']);
    Route::put('/{id}', [ReportController::class, 'update']);
});
Route::group([
    'prefix' => 'properties',
    'middleware' => ['auth_user:api',]
], function () {
    Route::post('/', [PropertyController::class, 'store']);
    Route::get('/', [PropertyController::class, 'index']);
    Route::post('/{id}', [PropertyController::class, 'show']);
    Route::put('/{id}', [PropertyController::class, 'update']);
});
Route::get('governorates', [GovernorateController::class, 'index']);
Route::get('propertyStates', [PropertyStateController::class, 'index']);
Route::get('governorates/{id}/regions', [RegionController::class, 'index']);
Route::get('reservationStates', [ReservationStateController::class, 'index']);
Route::get('reservationTypes', [ReservationTypeController::class, 'index']);
