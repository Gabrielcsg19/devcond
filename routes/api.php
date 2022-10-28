<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;
use Illuminate\Support\Facades\Route;

Route::get('ping', function() {
    return [
        'pong' => true
    ];
});

Route::get('401', [AuthController::class, 'unauthorized'])->name('login');
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function() {
    Route::post('auth/validate', [AuthController::class, 'validateToken']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Mural de avisos
    Route::get('walls', [WallController::class, 'getAll']);
    Route::post('wall/{id}/like', [WallController::class, 'like']);

    // Documentos
    Route::get('docs', [DocController::class, 'getAll']);

    // Livro de ocorrências
    Route::get('warnings/logged-user', [WarningController::class, 'getLoggedUserWarnings']);
    Route::post('warning', [WarningController::class, 'create']);
    Route::post('warning/file', [WarningController::class, 'insertFile']);

    // Boletos
    Route::get('billets', [BilletController::class, 'getAll']);

    // Achados e Perdidos
    Route::prefix('found-and-lost')->group(function() {
        Route::get('', [FoundAndLostController::class, 'getAll']);
        Route::post('', [FoundAndLostController::class, 'create']);
        Route::put('{id}', [FoundAndLostController::class, 'update']);
    });

    // Unidade
    Route::prefix('unit')->group(function() {
        Route::get('{id}', [UnitController::class, 'getInfo']);
        Route::post('{id}/people', [UnitController::class, 'insertPeople']);
        Route::post('{id}/vehicle', [UnitController::class, 'insertVehicle']);
        Route::post('{id}/pet', [UnitController::class, 'insertPet']);
        Route::delete('{unit_id}/people/{id}', [UnitController::class, 'deletePeople']);
        Route::delete('{unit_id}/vehicle/{id}', [UnitController::class, 'deleteVehicle']);
        Route::delete('{unit_id}/pet/{id}', [UnitController::class, 'deletePet']);
    });

    // Reservas
    Route::get('reservations', [ReservationController::class, 'getAll']);
    Route::get('reservations/logged-user', [ReservationController::class, 'getLoggedUserReservations']);
    Route::prefix('reservation')->group(function() {
        Route::post('{area_id}', [ReservationController::class, 'create']);
        Route::get('{area_id}/disabled-dates', [ReservationController::class, 'getDisabledDates']);
        Route::get('{area_id}/available-times', [ReservationController::class, 'getAvailableTimes']);
        Route::delete('{id}/logged-user', [ReservationController::class, 'deleteLoggedUserReservation']);
    });
});