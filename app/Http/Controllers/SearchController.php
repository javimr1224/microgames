<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        $games = Game::where('name', 'regexp', "/.*" . preg_quote($query) . "/i")->get();
        return response()->json(['results' => $games]);
    }
}