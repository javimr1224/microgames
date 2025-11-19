<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');
    
    Route::get('/games', function () {
        return view('admin.games');
    })->name('games');

    Route::get('/matches', function () {
        return view('admin.matches');
    })->name('matches');

    Route::get('/incomes', function () {
        return view('admin.incomes');
    })->name('incomes'); 

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    Route::get('/pages', function () {
        return view('admin.pages');
    })->name('pages');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/login', [App\Http\Controllers\AdminLoginController::class, 'create'])->name('admin.login');

Route::post('/admin/login', [App\Http\Controllers\AdminLoginController::class, 'store']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/game-menu', function () {
    return redirect(env('FRONTEND_URL', 'http://localhost:3000') . '/menu'); 
})->name('game-menu');

// New Routes for Games Dropdown
Route::get('/games/action', function () {
    return '<h1>Action Games Page</h1>';
})->name('games.action');

Route::get('/games/puzzle', function () {
    return '<h1>Puzzle Games Page</h1>';
})->name('games.puzzle');

Route::get('/games/adventure', function () {
    return '<h1>Adventure Games Page</h1>';
})->name('games.adventure');

// New Routes for About Us Dropdown
Route::get('/about/team', function () {
    return '<h1>Our Team Page</h1>';
})->name('about.team');

Route::get('/about/contact', function () {
    return '<h1>Contact Us Page</h1>';
})->name('about.contact');


require __DIR__.'/auth.php';
