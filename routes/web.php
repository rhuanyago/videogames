<?php

use App\Http\Controllers\GamesController;
use Illuminate\Support\Facades\Route;

Route::controller('games', GamesController::class)
    ->name('games.')
    ->group(function () {
        Route::get('/', [GamesController::class, 'index'])->name('index');
        Route::get('games/{slug}', [GamesController::class, 'show'])->name('show');
    });
