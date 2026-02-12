<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
            return $request->user();
        }
        );

        Route::get('/game/init', [GameController::class , 'initialData']);
        Route::post('/game/click', [GameController::class , 'click']);
        Route::post('/game/upgrade', [GameController::class , 'buyUpgrade']);
        Route::post('/game/attack', [GameController::class , 'attack']);
        Route::get('/game/leaderboard', [GameController::class , 'leaderboard']);
    });