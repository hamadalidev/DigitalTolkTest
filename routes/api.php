<?php

use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/translations', [TranslationController::class, 'index']);
    Route::post('/translations', [TranslationController::class, 'store']);
    Route::get('/translations/{id}', [TranslationController::class, 'show'])->whereNumber('id');
    Route::put('/translations/{id}', [TranslationController::class, 'update'])->whereNumber('id');
    Route::delete('/translations/{id}', [TranslationController::class, 'destroy'])->whereNumber('id');
    Route::get('/translations/locale/{locale}', [TranslationController::class, 'getByLocale']);
    Route::get('/translations/json/{locale}', [TranslationController::class, 'getJsonTranslations']);
});

Route::post('/login', [LoginController::class, 'login']);
