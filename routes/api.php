<?php

use App\Http\Controllers\ScoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function (Request $request) {
    return response()->json(['message' => ' ']);
});

Route::get('/scores/{game_id}', [ScoreController::class, 'index']);
Route::get('/users/{user_id}/scores', [ScoreController::class, 'userScores']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/scores', [ScoreController::class, 'store']);
});
