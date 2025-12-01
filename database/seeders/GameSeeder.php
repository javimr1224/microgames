<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game; // Import the Game model
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::truncate();

        $games = [
            [
                'name' => 'Breakout',
                'description' => 'Un juego clásico de arcade en el que controlas una pala para romper ladrillos.',
                'image' => '/images/breakout.png',
                'video' => '/videos/video-brekout.png',
                'file' => 'breakout',
                'category' => 'Arcade',
                'visits' => rand(100, 500),
                'recommended' => true,
            ],
            [
                'name' => 'Snake',
                'description' => 'Guía a una serpiente para que coma y crezca más, evitando chocar contra sí misma o las paredes.',
                'image' => '/images/snake.png',
                'video' => '/videos/video-snake.png',
                'file' => 'snake',
                'category' => 'Arcade',
                'visits' => rand(100, 500),
                'recommended' => false,
            ],
            [
                'name' => 'Tetris',
                'description' => 'Un juego de rompecabezas donde encajas piezas que caen para completar líneas.',
                'image' => '/images/tetris.png',
                'video' => '/videos/video-tetris.png',
                'file' => 'tetris',
                'category' => 'Arcade',
                'visits' => rand(100, 500),
                'recommended' => true,
            ],
            [
                'name' => 'Pong',
                'description' => 'Uno de los primeros videojuegos de arcade, un juego de deportes en 2D que simula el tenis de mesa.',
                'image' => '/images/video-pong.png',
                'video' => '/videos/video-pong.png',
                'file' => 'pong',
                'category' => 'Arcade',
                'visits' => rand(100, 500),
                'recommended' => false,
            ],
            [
                'name' => 'Coming Soon',
                'description' => '¡Prepárate! Este emocionante juego estará disponible muy pronto.',
                'image' => '/images/pixel-trendy.png',
                'video' => null,
                'file' => null,
                'category' => 'Novedades',
                'visits' => 0,
                'recommended' => true,
            ],
        ];

        foreach ($games as $gameData) {
            Game::create($gameData);
        }
    }
}