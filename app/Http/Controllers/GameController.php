<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class GameController extends Controller
{
    private function getPurchasedGameIds()
    {
        return Auth::check() ? (Auth::user()->purchased_game_ids ?? []) : [];
    }

    public function index()
    {
        $games = Game::where('name', 'Skybound')->get();
        $purchasedGameIds = $this->getPurchasedGameIds();

        return view('storeGames', ['games' => $games, 'purchasedGameIds' => $purchasedGameIds]);
    }

    public function filter($filter)
    {
        $gamesQuery = Game::query();

        switch ($filter) {
            case 'popular':
                $gamesQuery->orderBy('visits', 'desc');
                break;
            case 'newest':
                $gamesQuery->orderBy('created_at', 'desc');
                break;
            case 'recommended':
                $gamesQuery->where('recommended', true);
                break;
            default:
                $games = Game::all();
                $purchasedGameIds = $this->getPurchasedGameIds();

                return view('storeGames', ['games' => $games, 'purchasedGameIds' => $purchasedGameIds]);
        }

        $games = $gamesQuery->get();
        $purchasedGameIds = $this->getPurchasedGameIds();

        return view('storeGames', ['games' => $games, 'filter' => $filter, 'purchasedGameIds' => $purchasedGameIds]);
    }

    public function getCategories()
    {
        $categories = Game::distinct('category')->get();

        return response()->json($categories);
    }

    public function gamesByCategory($category)
    {
        $games = Game::where('category', 'LIKE', '%'.$category.'%')->get();
        $purchasedGameIds = $this->getPurchasedGameIds();

        return view('gamesByCategory', ['games' => $games, 'category' => $category, 'purchasedGameIds' => $purchasedGameIds]);
    }

    public function show(Game $game)
    {
        $purchasedGameIds = $this->getPurchasedGameIds();

        return view('showGame', ['game' => $game, 'purchasedGameIds' => $purchasedGameIds]);
    }

    public function launch(Game $game)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para jugar a este juego.');
        }

        $user = Auth::user();
        if (! in_array((string) $game->id, $user->purchased_game_ids ?? [])) {
            return redirect()->route('games.show', $game)->with('error', 'No has comprado este juego.');
        }

        if (! $game->file) {
            return redirect()->back()->with('error', 'Este juego no está disponible para jugar.');
        }

        return Redirect::to(rtrim(config('app.frontend_url'), '/').'/play/'.$game->file);
    }

    public function myGames()
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus juegos.');
        }

        $user = Auth::user();
        $purchasedGameIds = $user->purchased_game_ids ?? [];
        $purchasedGames = Game::findMany($purchasedGameIds);

        return view('my-games', ['purchasedGames' => $purchasedGames]);
    }

    public function myGamesApi()
    {
        $user = Auth::user();
        $purchasedGameIds = $user->purchased_game_ids ?? [];
        $purchasedGames = Game::findMany($purchasedGameIds);

        return response()->json($purchasedGames);
    }

    public function filterApi($filter)
    {
        $gamesQuery = Game::query();

        switch ($filter) {
            case 'popular':
                $gamesQuery->orderBy('visits', 'desc');
                break;
            case 'newest':
                $gamesQuery->orderBy('created_at', 'desc');
                break;
            case 'recommended':
                $gamesQuery->where('recommended', true);
                break;
            case 'all':
                break;
            default:
                $gamesQuery->orderBy('created_at', 'desc');
        }

        $games = $gamesQuery->get();

        return response()->json($games);
    }

    public function showApi(Game $game)
    {
        return response()->json($game);
    }
}
