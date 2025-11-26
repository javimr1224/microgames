<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalGames = Game::count();
        $todaySessions = Score::where('created_at', '>=', Carbon::today())->count();
        $revenue = 0; // Placeholder
        $recentActivities = []; // Placeholder

        return view('admin.dashboard', compact('totalUsers', 'totalGames', 'todaySessions', 'revenue', 'recentActivities'));
    }
}
