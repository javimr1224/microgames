import React from 'react';
import { Card } from './ui/card';
import { Button } from './ui/button';
import { Gamepad2, Trophy, Zap, Target } from 'lucide-react';
import { GameType } from '../App';

interface GameMenuProps {
  onSelectGame: (game: GameType) => void;
  scores: Record<string, number>;
}

export function GameMenu({ onSelectGame, scores }: GameMenuProps) {
  const games = [
    {
      id: 'snake' as const,
      name: 'SNAKE',
      description: 'Come la comida y crece sin tocarte',
      icon: <Zap className="w-8 h-8" />,
      color: 'from-green-500 to-emerald-600',
      glowColor: 'shadow-green-500/50'
    },
    {
      id: 'pong' as const,
      name: 'PONG',
      description: 'Clásico juego de ping pong',
      icon: <Target className="w-8 h-8" />,
      color: 'from-blue-500 to-cyan-600',
      glowColor: 'shadow-blue-500/50'
    },
    {
      id: 'tetris' as const,
      name: 'TETRIS',
      description: 'Encaja las piezas perfectamente',
      icon: <Gamepad2 className="w-8 h-8" />,
      color: 'from-purple-500 to-violet-600',
      glowColor: 'shadow-purple-500/50'
    },
    {
      id: 'breakout' as const,
      name: 'BREAKOUT',
      description: 'Rompe todos los bloques',
      icon: <Trophy className="w-8 h-8" />,
      color: 'from-orange-500 to-red-600',
      glowColor: 'shadow-orange-500/50'
    }
  ];

  return (
    <div className="min-h-screen flex flex-col items-center justify-center p-4">
      <div className="text-center mb-12">
        <h1 className="text-6xl md:text-8xl tracking-wider mb-4 bg-gradient-to-r from-cyan-400 via-red-300 to-yellow-400 bg-clip-text text-transparent animate-pulse">
          MicroGames
        </h1>
        <h2 className="text-3xl md:text-4xl tracking-wider text-white mb-2" style={{ fontFamily: 'Press Start 2P, monospace' }}>
          ARCADE
        </h2>
        <div className="w-32 h-1 bg-gradient-to-r from-cyan-400 to-pink-400 mx-auto"></div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl w-full">
        {games.map((game) => (
          <Card 
            key={game.id}
            className={`relative group cursor-pointer transition-all duration-300 hover:scale-105 border-2 border-gray-600 bg-gray-900/80 backdrop-blur-sm hover:border-white hover:shadow-2xl ${game.glowColor}`}
            onClick={() => onSelectGame(game.id)}
          >
            <div className="p-6">
              <div className={`absolute inset-0 bg-gradient-to-br ${game.color} opacity-10 group-hover:opacity-20 transition-opacity duration-300`}></div>
              
              <div className="relative z-10">
                <div className="flex items-center justify-between mb-4">
                  <div className={`p-3 rounded-lg bg-gradient-to-br ${game.color} text-white shadow-lg`}>
                    {game.icon}
                  </div>
                  <div className="text-right">
                    <div className="text-xs text-gray-400" style={{ fontFamily: 'Press Start 2P, monospace' }}>RECORD</div>
                    <div className="text-xl text-yellow-400" style={{ fontFamily: 'Press Start 2P, monospace' }}>{scores[game.id]}</div>
                  </div>
                </div>
                
                <h3 className="text-2xl tracking-wider text-white mb-2 group-hover:text-transparent group-hover:bg-gradient-to-r group-hover:from-cyan-400 group-hover:to-pink-400 group-hover:bg-clip-text transition-all duration-300" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  {game.name}
                </h3>
                
                <p className="text-gray-300 text-sm mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  {game.description}
                </p>
                
                <Button 
                  className={`w-full tracking-wider bg-gradient-to-r ${game.color} hover:shadow-lg hover:shadow-current/50 transition-all duration-300 border-0`}
                  style={{ fontFamily: 'Press Start 2P, monospace' }}
                  size="lg"
                >
                  JUGAR
                </Button>
              </div>
            </div>
          </Card>
        ))}
      </div>

      <div className="mt-12 text-center">
        <p className="text-gray-400 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
          Usa las teclas de dirección para moverte • ESC para pausar
        </p>
      </div>
    </div>
  );
}