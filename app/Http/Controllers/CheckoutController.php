<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout()
    {
        Stripe::setApiKey(config('stripe.secret'));

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar una compra.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Tu carrito está vacío.');
        }

        $line_items = [];
        $game_ids_in_cart = [];

        $games = Game::findMany(array_keys($cart));

        foreach ($games as $game) {
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $game->name,
                        'images' => [asset($game->image)],
                    ],
                    'unit_amount' => $game->price * 100, 
                ],
                'quantity' => $cart[$game->id]['quantity'],
            ];
            $game_ids_in_cart[] = (string)$game->id;
        }

        $checkoutSession = Session::create([
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'user_id' => (string)$user->id, 
                'game_ids' => json_encode($game_ids_in_cart), 
            ],
            'client_reference_id' => (string)$user->id,
        ]);

        return redirect()->away($checkoutSession->url);
    }

    public function success(Request $request)
    {
        Stripe::setApiKey(config('stripe.secret'));
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('checkout.cancel')->with('error', 'Falta el ID de la sesión de pago.');
        }

        try {
            $checkoutSession = Session::retrieve($sessionId);

            if ($checkoutSession->payment_status !== 'paid') {
                return redirect()->route('checkout.cancel')->with('error', 'El pago no fue exitoso. Estado: ' . $checkoutSession->payment_status);
            }

            $userId = $checkoutSession->metadata->user_id;
            
            $gameIdsRaw = $checkoutSession->metadata->game_ids;
            $gameIds = is_string($gameIdsRaw) ? json_decode($gameIdsRaw, true) : (array) $gameIdsRaw;


            $user = User::find($userId);

            if ($user && is_array($gameIds)) {
                foreach ($gameIds as $gameId) {
                    if (!in_array($gameId, $user->purchased_game_ids ?? [])) {
                        $user->push('purchased_game_ids', $gameId, true);
                    }
                }
            }

            session()->forget('cart'); // Clear cart after successful purchase
            return view('checkout.success');

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return redirect()->route('checkout.cancel')->with('error', 'Error de API de Stripe: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('checkout.cancel')->with('error', 'Ocurrió un error inesperado: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}