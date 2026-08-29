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

        return view('profile.show', [
            'user' => $user,
            'purchased_games' => $purchased_games,
        ]);
    }
}
