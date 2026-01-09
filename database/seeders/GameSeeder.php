<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                'slug' => Str::slug('Breakout'),
                'description' => 'Un juego clásico de arcade en el que controlas una pala para romper ladrillos.',
                'image' => '/images/breakout.png',
                'video' => '/videos/video-breakout.png',
                'file' => 'breakout',
                'category' => 'Arcade',
                'recommended' => true,
            ],
            [
                'name' => 'Snake',
                'slug' => Str::slug('Snake'),
                'description' => 'Guía a una serpiente para que coma y crezca más, evitando chocar contra sí misma o las paredes.',
                'image' => '/images/snake.png',
                'video' => '/videos/video-snake.png',
                'file' => 'snake',
                'category' => 'Arcade',
                'recommended' => false,
            ],
            [
                'name' => 'Tetris',
                'slug' => Str::slug('Tetris'),
                'description' => 'Un juego de rompecabezas donde encajas piezas que caen para completar líneas.',
                'image' => '/images/tetris.png',
                'video' => '/videos/video-tetris.png',
                'file' => 'tetris',
                'category' => 'Arcade',
                'recommended' => true,
            ],
            [
                'name' => 'Pong',
                'slug' => Str::slug('Pong'),
                'description' => 'Uno de los primeros videojuegos de arcade, un juego de deportes en 2D que simula el tenis de mesa.',
                'image' => '/images/video-pong.png',
                'video' => '/videos/video-pong.png',
                'file' => 'pong',
                'category' => 'Arcade',
                'recommended' => false,
            ],
            [
                'name' => 'Coming Soon',
                'slug' => Str::slug('Coming Soon'),
                'description' => '¡Prepárate! Este emocionante juego estará disponible muy pronto.',
                'image' => '/images/pixel-trendy.png',
                'video' => null,
                'file' => null,
                'category' => 'Novedades',
                'visits' => 0,
                'recommended' => true,
            ],
            [
                'name' => 'Skybound',
                'slug' => Str::slug('Skybound'),
                'description' => 'Un juego de aventuras épico donde vuelas por islas flotantes.',
                'image' => '/images/skybound-banner.png',
                'video' => null,
                'file' => 'skybound',
                'category' => 'Aventura',
                'recommended' => true,
                'price' => 3.99,
                'stripe_price_id' => null,
            ],
        ];

        foreach ($games as $gameData) {
            Game::create($gameData);
        }
    }
}