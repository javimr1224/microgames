<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileViewController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $purchased_games = Game::whereIn('_id', $user->purchased_game_ids ?? [])->get();

        $purchased_games->each(function ($game) {
            if ($game->image && !str_starts_with($game->image, 'http')) {
                $game->image = asset('images/' . $game->image);
            }
            if ($game->video && !str_starts_with($game->video, 'http')) {
                $game->video = asset('videos/' . $game->video);
            }
        });

        return view('profile.show', [
            'user' => $user,
            'purchased_games' => $purchased_games,
        ]);
    }
}
