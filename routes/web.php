<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users');

    Route::get('/games', [AdminGameController::class, 'index'])->name('games');

    Route::get('/matches', [MatchController::class, 'index'])->name('matches');

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
    return redirect(env('FRONTEND_URL', 'http://localhost:3000'));
})->name('game-menu');

Route::get('/store', [GameController::class, 'index'])->name('storeGames');
Route::get('/store/filter/{filter}', [GameController::class, 'filter'])->name('store.filter');

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/categories', [GameController::class, 'getCategories'])->name('categories.index');

Route::get('/games/{category}', [GameController::class, 'gamesByCategory'])->name('games.byCategory');

Route::get('/game/show/{game}', [GameController::class, 'show'])->middleware('game.visits')->name('games.show');



Route::view('/help', 'help')->name('help');

Route::get('/about/team', function () {
    return '<h1>Our Team Page</h1>';
})->name('about.team');

Route::get('/about/contact', function () {
    return '<h1>Contact Us Page</h1>';
})->name('about.contact');

Route::get('/support', function () {
    return view('support');
})->name('support');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

require __DIR__.'/auth.php';

