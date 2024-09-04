<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\MainController::class, 'index']);
Route::get('/tabula', [App\Http\Controllers\TabulaController::class, 'index']);
