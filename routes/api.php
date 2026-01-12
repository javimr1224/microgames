<?php

use App\Http\Controllers\ScoreController;
use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response()->json(['message' => ' ']);
});

Route::get('/scores/{game_id}', [ScoreController::class, 'index']);
Route::get('/users/{user_id}/scores', [ScoreController::class, 'userScores']);

Route::get('/games/filter/{filter}', [GameController::class, 'filterApi'])->name('api.games.filter');
Route::get('/games/{game:slug}', [GameController::class, 'showApi'])->name('api.games.show');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/scores', [ScoreController::class, 'store']);
    Route::get('/my-games', [GameController::class, 'myGamesApi']); 
});
