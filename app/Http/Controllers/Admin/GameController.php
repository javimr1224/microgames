<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Support\MediaStorage;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::all();

        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'video' => 'nullable|file|mimes:mp4,webm,gif|max:10240',
            'file' => 'required|string',
            'category' => 'required|string',
            'price' => 'nullable|numeric',
            'stripe_price_id' => 'nullable|string',
            'recommended' => 'nullable|boolean',
        ]);

        $data = $request->except(['image', 'video']);

        if ($request->hasFile('image')) {
            $data['image'] = MediaStorage::store($request->file('image'), 'images');
        }

        if ($request->hasFile('video')) {
            $data['video'] = MediaStorage::store($request->file('video'), 'videos');
        }

        $data['recommended'] = $request->has('recommended');

        Game::create($data);

        return redirect()->route('admin.games.index')->with('success', 'Juego creado con éxito.');
    }

    public function edit(Game $game)
    {
        return view('admin.games.edit', compact('game'));
    }

    public function show(Game $game)
    {
        return view('admin.games.show', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'video' => 'nullable|file|mimes:mp4,webm,gif|max:10240',
            'file' => 'required|string',
            'category' => 'required|string',
            'price' => 'nullable|numeric',
            'stripe_price_id' => 'nullable|string',
            'recommended' => 'nullable|boolean',
        ]);

        $data = $request->except(['image', 'video']);

        if ($request->hasFile('image')) {
            $image = MediaStorage::store($request->file('image'), 'images');
            MediaStorage::delete($game->image);
            $data['image'] = $image;
        }

        if ($request->hasFile('video')) {
            $video = MediaStorage::store($request->file('video'), 'videos');
            MediaStorage::delete($game->video);
            $data['video'] = $video;
        }

        $data['recommended'] = $request->has('recommended');

        $game->update($data);

        return redirect()->route('admin.games.index')->with('success', 'Juego actualizado con éxito.');
    }

    public function destroy(Game $game)
    {
        MediaStorage::delete($game->image);
        MediaStorage::delete($game->video);
        $game->delete();

        return redirect()->route('admin.games.index')->with('success', 'Juego eliminado con éxito.');
    }
}
