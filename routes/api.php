<?php

use App\Http\Controllers\HuespedController;
use App\Http\Controllers\MultaController;

Route::post('/register', [HuespedController::class, 'register']);
Route::post('/login', [HuespedController::class, 'login']);
Route::get('/huespedes', [HuespedController::class, 'list']);

Route::post('/multas', [MultaController::class, 'store']);
Route::get('/multas/huesped/reciente/{id}', [MultaController::class, 'multaRecientePorHuesped']);
Route::get('/multas/huesped/{id}', [MultaController::class, 'multasPorHuesped']);