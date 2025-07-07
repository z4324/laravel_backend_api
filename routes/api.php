<?php

use App\Http\Controllers\HuespedController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\SessionController;
use Illuminate\Http\Request;

/* Route::middleware('auth:sanctum')->get('/user', function () {
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

 */

Route::post('/register', [HuespedController::class, 'register']);
Route::post('/login', [HuespedController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/huespedes', [HuespedController::class, 'list']);
    Route::post('/multas', [MultaController::class, 'store']);
    Route::get('/multas/huesped/{id}', [MultaController::class, 'multasPorHuesped']);
    Route::post('/multas/{id}/vista', [MultaController::class, 'marcarComoVista']);
    Route::get('/multas/reciente/{id}', [MultaController::class, 'multaRecientePorHuesped']);
    Route::get('/sesiones', [SessionController::class, 'index']);
    Route::delete('/sesiones/{id}', [SessionController::class, 'destroy']);
    Route::delete('/sesiones', [SessionController::class, 'destroyAllExceptCurrent']);
});