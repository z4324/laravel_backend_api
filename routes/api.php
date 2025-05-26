<?php
use App\Http\Controllers\HuespedController;

Route::post('/register', [HuespedController::class, 'register']);
Route::post('/login', [HuespedController::class, 'login']);
Route::get('/huespedes', [HuespedController::class, 'list']);

use App\Http\Controllers\MultaController;

Route::post('/multas', [MultaController::class, 'store']);
Route::get('/multas/huesped/{id}', [MultaController::class, 'multasPorHuesped']);