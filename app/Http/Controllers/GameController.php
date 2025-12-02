<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
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
        $games = Game::all(); // Get all games for the main store page
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
                // If an unknown filter is provided, return all games
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
        $games = Game::where('category', 'LIKE', '%' . $category . '%')->get();
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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para jugar a este juego.');
        }

        $user = Auth::user();
        if (!in_array((string)$game->id, $user->purchased_game_ids ?? [])) {
            return redirect()->route('games.show', $game)->with('error', 'No has comprado este juego.');
        }

        if (!$game->file) {
            return redirect()->back()->with('error', 'Este juego no está disponible para jugar.');
        }

        // Redirect to the frontend URL for the game
        return Redirect::to(env('FRONTEND_URL', 'http://localhost:3000') . '/' . $game->file);
    }

    public function myGames()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus juegos.');
        }

        $user = Auth::user();
        $purchasedGameIds = $user->purchased_game_ids ?? [];
        $purchasedGames = Game::findMany($purchasedGameIds);

        return view('my-games', ['purchasedGames' => $purchasedGames]);
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
            case 'all': // New case for clearing filters
                // No specific filtering, just get all games
                break;
            default:
                $gamesQuery->orderBy('created_at', 'desc'); // Default to newest if no valid filter
        }

        $games = $gamesQuery->get();

        // Prepend the full asset URL to the image and video paths
        $games->each(function ($game) {
            if ($game->image && !str_starts_with($game->image, 'http')) {
                $game->image = asset($game->image);
            }
            if ($game->video && !str_starts_with($game->video, 'http')) {
                $game->video = asset($game->video);
            }
        });

        return response()->json($games);
    }
}