<?php
use App\Http\Controllers\HuespedController;

Route::post('/register', [HuespedController::class, 'register']);
Route::post('/login', [HuespedController::class, 'login']);