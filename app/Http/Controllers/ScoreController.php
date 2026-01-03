<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    /**
     * Display a listing of the scores for a specific game.
     */
    public function index($game_id)
    {
        $scores = Score::where('game_id', $game_id)
                       ->orderBy('score', 'desc')
                       ->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created score in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|string',
            'score' => 'required|integer',
        ]);

        // Find the game by its name (which is sent as game_id from frontend)
        $game = \App\Models\Game::where('name', 'like', $request->game_id)->first();

        if (!$game) {
            return response()->json(['message' => 'Game not found'], 404);
        }

        $userId = Auth::id();
        // Use the actual game _id
        $gameId = $game->_id;
        $newScore = $request->score;

        $existingScore = Score::where('user_id', $userId)
            ->where('game_id', $gameId)
            ->first();

        if ($existingScore) {
            if ($newScore > $existingScore->score) {
                $existingScore->score = $newScore;
                $existingScore->save();
            }
            return response()->json($existingScore, 200);
        } else {
            $score = Score::create([
                'user_id' => $userId,
                'game_id' => $gameId,
                'score' => $newScore,
            ]);

            return response()->json($score, 201);
        }
    }

    /**
     * Display a listing of the scores for a specific user.
     */
    public function userScores($user_id)
    {
        $scores = Score::where('user_id', $user_id)
                       ->orderBy('score', 'desc')
                       ->get();

        return response()->json($scores);
    }
}
