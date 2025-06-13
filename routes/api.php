<?php

use App\Http\Controllers\HuespedController;
use App\Http\Controllers\MultaController;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user', function () {
    return response()->json(auth()->user());
});

Route::post('/login', function (Request $request) {
    $user = User::find(100);

    $token = $user->createToken('token')->plainTextToken;
    return response()->json([
        'token' => $token,
        'status' => 200
    ]);
});



Route::post('/register', [HuespedController::class, 'register']);
Route::post('/login', [HuespedController::class, 'login']);
Route::get('/huespedes', [HuespedController::class, 'list']);

Route::post('/multas', [MultaController::class, 'store']);
Route::get('/multas/huesped/reciente/{id}', [MultaController::class, 'multaRecientePorHuesped']);
Route::get('/multas/huesped/{id}', [MultaController::class, 'multasPorHuesped']);
Route::put('/multas/{id}/vista', [MultaController::class, 'marcarComoVista']);