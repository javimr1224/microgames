<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Score;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index()
    {
        $scores = Score::with('user', 'game')->get();
        return view('admin.matches', compact('scores'));
    }
}
