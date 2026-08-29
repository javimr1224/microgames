<?php

namespace App\Http\Controllers;

use App\Models\Game;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $games = [];

        if ($cart) {
            $game_ids = array_keys($cart);
            $games = Game::findMany($game_ids);
            foreach ($games as $game) {
                $total += $game->price * $cart[$game->id]['quantity'];
            }
        }

        return view('cart', compact('games', 'cart', 'total'));
    }

    public function add(Game $game)
    {
        $cart = session()->get('cart', []);

        if (! isset($cart[$game->id])) {
            $cart[$game->id] = [
                'quantity' => 1,
            ];
        } else {
            // Optionally, you could increase the quantity here if you allow multiple copies
            // $cart[$game->id]['quantity']++;
        }

        session()->put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Juego añadido al carrito!');
    }

    public function remove($id)
    {
        $cart = session()->get('cart');
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Juego eliminado del carrito.');
    }
}
