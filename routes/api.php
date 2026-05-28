<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocioApiController;
use Illuminate\Support\Facades\Route;

Route::get('club',      [SocioApiController::class, 'club']);
Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:api-login');
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:api-register');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout',            [AuthController::class, 'logout']);
    Route::get('me',                 [SocioApiController::class, 'me']);
    Route::patch('me',               [SocioApiController::class, 'update']);
    Route::post('me/password',       [SocioApiController::class, 'changePassword']);
    Route::post('me/push-token',     [SocioApiController::class, 'savePushToken']);
    Route::get('cuotas',             [SocioApiController::class, 'cuotas']);
    Route::get('noticias',           [SocioApiController::class, 'noticias']);
    Route::post('noticias/marcar-leidas', [SocioApiController::class, 'marcarNoticiasLeidas']);
    Route::get('disciplinas',        [SocioApiController::class, 'disciplinas']);
    Route::get('calendario',                          [SocioApiController::class, 'calendario']);
    Route::post('disciplinas/{disciplina}/inscribir', [SocioApiController::class, 'inscribirDisciplina']);
    Route::patch('disciplinas/{disciplina}/baja',     [SocioApiController::class, 'bajaDisciplina']);
    Route::get('ingresos',           [SocioApiController::class, 'ingresos']);
});
