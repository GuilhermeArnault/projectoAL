<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AlojamentoController;
use App\Http\Controllers\API\ReservaController;
use App\Http\Controllers\API\ComentarioController;
use App\Http\Controllers\API\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/alojamentos', [AlojamentoController::class, 'index']);
Route::get('/alojamentos/{id}', [AlojamentoController::class, 'show']);
Route::post('/alojamentos/{id}/available', [ReservaController::class, 'available']);
Route::get('/alojamentos/{id}/comentarios', [ComentarioController::class, 'index']);

// Rotas protegidas (autenticado)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::get('/reservas/me', [ReservaController::class, 'myReservations']);
    Route::post('/comentarios', [ComentarioController::class, 'store']);
});

// Rotas do admin
Route::middleware(['auth:sanctum', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/reservas', [AdminController::class, 'reservas']);
    Route::patch('/reservas/{id}/estado', [AdminController::class, 'alterarEstado']);
    Route::get('/relatorios/ocupacao', [AdminController::class, 'relatorioOcupacao']);
});
