<?php

use App\Http\Controllers\HuespedController;
use App\Http\Controllers\MultaController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CodigoSeguridadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/test-middleware', function () {
    return response()->json(['message' => 'Middleware test-role funcionando correctamente']);
})->middleware(['auth:sanctum', 'test-role']);

Route::get('/test-middleware-full', function () {
    return response()->json(['message' => 'Middleware CheckRoleHuesped funcionando correctamente']);
})->middleware(['auth:sanctum', \App\Http\Middleware\CheckRoleHuesped::class]);

RUTAS PARATESTEAR EL MIDDLEWARE CheckRoleHuesped
 */

Route::post('/register', [HuespedController::class, 'register']);
Route::post('/login', [HuespedController::class, 'login'])->name('login');
Route::post('/cambiar-contrasena', [HuespedController::class, 'actualizarContrasena']);
Route::post('/enviar-codigo', [CodigoSeguridadController::class, 'enviarCodigo']);
Route::post('/validar-codigo', [CodigoSeguridadController::class, 'validarCodigo']);


Route::middleware(['auth:sanctum', \App\Http\Middleware\CheckRoleHuesped::class])->group(function () {
    Route::get('/huespedes', [HuespedController::class, 'list']);
    Route::post('/multas', [MultaController::class, 'store']);
    Route::get('/multas/huesped/{id}', [MultaController::class, 'multasPorHuesped']);
    Route::post('/multas/{id}/vista', [MultaController::class, 'marcarComoVista']);
    Route::get('/multas/reciente/{id}', [MultaController::class, 'multaRecientePorHuesped']);
    Route::get('/sesion', [SessionController::class, 'index']);
    Route::delete('/sesiones/{id}', [SessionController::class, 'destroy']);
    Route::delete('/sesiones', [SessionController::class, 'destroyAll']);
    Route::post('/actualizar-contrasena', [HuespedController::class, 'newPassword']);
    Route::post('/sesiones/cerrar-actual', [SessionController::class, 'destroyCurrent']);
    Route::post('/editar-perfil', [HuespedController::class, 'editarPerfil']);
    Route::post('/editar-datos', [HuespedController::class, 'editarDatos']);
    Route::post('/cambiar-correo', [HuespedController::class, 'cambiarCorreo']);
});