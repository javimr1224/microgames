<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalGames = Game::count();
        $todaySessions = Score::where('created_at', '>=', Carbon::today())->count();

        Stripe::setApiKey(config('stripe.secret'));
        $charges = Charge::all(['limit' => 100]);
        $revenue = array_reduce($charges->data, function ($carry, $charge) {
            if ($charge->status == 'succeeded') {
                return $carry + $charge->amount;
            }
            return $carry;
        }, 0) / 100;
        
        $recentActivities = [];

        return view('admin.dashboard', compact('totalUsers', 'totalGames', 'todaySessions', 'revenue', 'recentActivities'));
    }
}
