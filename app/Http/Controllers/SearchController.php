<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game; // Assuming a Game model exists

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        
        $games = Game::where('name', 'regexp', "/.*{$query}.*/i")->get();
        
        return response()->json([
            'results' => $games
        ]);
    }
}
