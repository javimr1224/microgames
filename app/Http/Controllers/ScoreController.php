<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        Log::info('Score store method called.', ['request' => $request->all()]);

        $request->validate([
            'game_id' => 'required|string',
            'score' => 'required|integer',
        ]);

        $game = \App\Models\Game::where('slug', $request->game_id)->first();

        if (!$game) {
            Log::warning('Game not found for slug.', ['slug' => $request->game_id]);
            return response()->json(['message' => 'Game not found'], 404);
        }
        Log::info('Game found.', ['game' => $game->toArray()]);

        $userId = Auth::id();
        Log::info('User ID retrieved.', ['user_id' => $userId]);

        if (!$userId) {
            Log::error('User is not authenticated.');
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        
        $gameId = $game->_id;
        $newScore = $request->score;

        Log::info('Searching for existing score.', ['user_id' => $userId, 'game_id' => $gameId]);
        $existingScore = Score::where('user_id', $userId)
            ->where('game_id', $gameId)
            ->first();

        if ($existingScore) {
            Log::info('Existing score found.', ['score' => $existingScore->toArray()]);
            if (!is_null($existingScore) && $newScore > $existingScore->score) {
                Log::info('New score is higher. Updating.', ['new_score' => $newScore]);
                $existingScore->score = $newScore;
                $existingScore->save();
                Log::info('Score updated successfully.');
            } else {
                if (is_null($existingScore)) {
                    Log::error('Logical contradiction: $existingScore is null inside if($existingScore) block.');
                } else {
                    Log::info('New score is not higher. Not updating.');
                }
            }
            return response()->json($existingScore, 200);
        } else {
            Log::info('No existing score found. Creating new score.');
            $score = Score::create([
                'user_id' => $userId,
                'game_id' => $gameId,
                'score' => $newScore,
            ]);
            Log::info('New score created successfully.', ['score' => $score->toArray()]);

            return response()->json($score, 201);
        }
    }

    /**
     * Display a listing of the scores for a specific user.
     */
    public function userScores($user_id)
    {
        $scores = Score::with('game')->where('user_id', $user_id)
                       ->orderBy('score', 'desc')
                       ->get();

        $transformedScores = $scores->map(function ($score) {
            if ($score->game) {
                return [
                    'game_id' => strtolower($score->game->name),
                    'score' => $score->score,
                    'user_id' => $score->user_id,
                ];
            }
            return null;
        })->filter();

        return response()->json($transformedScores->values());
    }
}
