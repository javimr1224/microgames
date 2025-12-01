<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::all(); // Get all games for the main store page
        return view('storeGames', ['games' => $games]);
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
                return view('storeGames', ['games' => $games]);
        }

        $games = $gamesQuery->get();

        return view('storeGames', ['games' => $games, 'filter' => $filter]);
    }

    public function getCategories()
    {
        $categories = Game::distinct('category')->get();
        return response()->json($categories);
    }

    public function gamesByCategory($category)
    {
        $games = Game::where('category', 'LIKE', '%' . $category . '%')->get();
        return view('gamesByCategory', ['games' => $games, 'category' => $category]);
    }

    public function show(Game $game)
    {
        return view('showGame', ['game' => $game]);
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