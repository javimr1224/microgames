<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function usersReport()
    {
        $users = User::all();
        $pdf = Pdf::loadView('admin.reports.users', compact('users'));
        return $pdf->download('users-report.pdf');
    }

    public function gamesReport()
    {
        $games = Game::all();
        $pdf = Pdf::loadView('admin.reports.games', compact('games'));
        return $pdf->download('games-report.pdf');
    }
}
