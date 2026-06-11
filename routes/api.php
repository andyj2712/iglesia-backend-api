<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MinistryController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\UserController;

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

// Rutas Públicas (Cualquiera puede acceder, no requieren token)
Route::post('/login', [AuthController::class, 'login']);

// Rutas Protegidas (Requieren Bearer Token)
Route::group(['middleware' => ['auth:sanctum']], function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    // Ruta para actualizar el token FCM del usuario (para notificaciones push)
    Route::post('/users/fcm-token', [App\Http\Controllers\API\UserController::class, 'updateFcmToken']);
    
    // Rutas estandar de tipo API para Ministerios (index, store, show, update, destroy)
    Route::apiResource('ministries', MinistryController::class);
    
    // Rutas personalizadas para la gestión de miembros del ministerio
    Route::post('ministries/{id}/assign', [MinistryController::class, 'assignUser']);
    Route::post('ministries/{id}/remove', [MinistryController::class, 'removeUser']);

    // Rutas para Calendarios y Asignaciones
    Route::get('schedules/my-schedules', [ScheduleController::class, 'mySchedules']);
    Route::apiResource('schedules', ScheduleController::class);


    // Rutas para el Muro de Anuncios y Reflexiones
    Route::apiResource('announcements', App\Http\Controllers\API\AnnouncementController::class);

    Route::group(['middleware' => ['auth:sanctum']], function () {
    // ... tus otras rutas ...
    
    // Rutas para la gestión de usuarios (Admin CRUD)
    Route::apiResource('users', UserController::class);
});

    Route::get('/user-profile', function (Request $request) {
        return $request->user()->load('role');
    });
});

