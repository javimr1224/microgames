import React, { useState, useEffect, useCallback } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { ArrowLeft, Play, Pause, RotateCcw } from 'lucide-react';

interface SnakeGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
}

interface Position {
  x: number;
  y: number;
}

const GRID_SIZE = 20;
const INITIAL_SNAKE = [{ x: 10, y: 10 }];
const INITIAL_FOOD = { x: 5, y: 5 };

export function SnakeGame({ onBack, onScore }: SnakeGameProps) {
  const [snake, setSnake] = useState<Position[]>(INITIAL_SNAKE);
  const [food, setFood] = useState<Position>(INITIAL_FOOD);
  const [direction, setDirection] = useState<Position>({ x: 0, y: 0 });
  const [gameRunning, setGameRunning] = useState(false);
  const [gameOver, setGameOver] = useState(false);
  const [score, setScore] = useState(0);

  const generateFood = useCallback(() => {
    const newFood = {
      x: Math.floor(Math.random() * GRID_SIZE),
      y: Math.floor(Math.random() * GRID_SIZE),
    };
    setFood(newFood);
  }, []);

  const resetGame = useCallback(() => {
    setSnake(INITIAL_SNAKE);
    setFood(INITIAL_FOOD);
    setDirection({ x: 0, y: 0 });
    setGameRunning(false);
    setGameOver(false);
    setScore(0);
  }, []);

  const startGame = () => {
    setGameRunning(true);
    setDirection({ x: 1, y: 0 });
  };

  const pauseGame = useCallback(() => {
    setGameRunning(prev => !prev);
  }, []);

  const checkCollision = useCallback((head: Position, snakeBody: Position[]) => {
    if (head.x < 0 || head.x >= GRID_SIZE || head.y < 0 || head.y >= GRID_SIZE) {
      return true;
    }
    return snakeBody.some(segment => segment.x === head.x && segment.y === head.y);
  }, []);

  const gameLoop = useCallback(() => {
    if (!gameRunning || gameOver) return;

    let shouldEndGame = false;
    let finalScore = 0;

    setSnake(prevSnake => {
      const newSnake = [...prevSnake];
      const head = { ...newSnake[0] };
      head.x += direction.x;
      head.y += direction.y;

      if (checkCollision(head, newSnake)) {
        setGameOver(true);
        setGameRunning(false);
        shouldEndGame = true;
        finalScore = score;
        return prevSnake;
      }

      newSnake.unshift(head);

      if (head.x === food.x && head.y === food.y) {
        setScore(prev => prev + 10);
        generateFood();
      } else {
        newSnake.pop();
      }

      return newSnake;
    });

    if (shouldEndGame) {
      onScore(finalScore);
    }
  }, [gameRunning, gameOver, direction, food, score, checkCollision, generateFood, onScore]);

  useEffect(() => {
    const interval = setInterval(gameLoop, 150);
    return () => clearInterval(interval);
  }, [gameLoop]);

  useEffect(() => {
    const handleKeyPress = (e: KeyboardEvent) => {
      if (!gameRunning) return;

      switch (e.key) {
        case 'ArrowUp':
          e.preventDefault();
          if (direction.y !== 1) setDirection({ x: 0, y: -1 });
          break;
        case 'ArrowDown':
          e.preventDefault();
          if (direction.y !== -1) setDirection({ x: 0, y: 1 });
          break;
        case 'ArrowLeft':
          e.preventDefault();
          if (direction.x !== 1) setDirection({ x: -1, y: 0 });
          break;
        case 'ArrowRight':
          e.preventDefault();
          if (direction.x !== -1) setDirection({ x: 1, y: 0 });
          break;
        case 'Escape':
          pauseGame();
          break;
      }
    };

    window.addEventListener('keydown', handleKeyPress);
    return () => window.removeEventListener('keydown', handleKeyPress);
  }, [direction, gameRunning, pauseGame]);

  return (
    <div className="min-h-screen p-4">
      <div className="text-center py-8">
        <h1 className="text-4xl md:text-6xl tracking-wider mb-4 bg-gradient-to-r from-green-400 via-emerald-400 to-green-500 bg-clip-text text-transparent">
          SNAKE
        </h1>
        <div className="w-24 h-1 bg-gradient-to-r from-green-400 to-emerald-400 mx-auto"></div>
      </div>

      <div className="flex items-center justify-center">
        <Card className="bg-gray-900/90 border-green-500 border-2 shadow-2xl shadow-green-500/50 backdrop-blur-sm">
          <div className="p-6">
            <div className="flex items-center justify-between mb-6">
              <Button 
                onClick={onBack}
                variant="outline" 
                size="sm"
                className="bg-red-600 hover:bg-red-700 border-red-500 text-white"
                style={{ fontFamily: 'Press Start 2P, monospace' }}
              >
                <ArrowLeft className="w-4 h-4 mr-2" />
                VOLVER
              </Button>
              
              <div className="text-center">
                <div className="text-yellow-400" style={{ fontFamily: 'Press Start 2P, monospace' }}>PUNTOS: {score}</div>
              </div>
              
              <div className="flex gap-2">
                {!gameRunning && !gameOver && (
                  <Button onClick={startGame} size="sm" className="bg-green-600 hover:bg-green-700">
                    <Play className="w-4 h-4" />
                  </Button>
                )}
                {gameRunning && (
                  <Button onClick={pauseGame} size="sm" className="bg-yellow-600 hover:bg-yellow-700">
                    <Pause className="w-4 h-4" />
                  </Button>
                )}
                <Button onClick={resetGame} size="sm" className="bg-red-600 hover:bg-red-700">
                  <RotateCcw className="w-4 h-4" />
                </Button>
              </div>
            </div>

          <div className="relative">
            <div 
              className="grid bg-black border-2 border-green-500 relative"
              style={{
                gridTemplateColumns: `repeat(${GRID_SIZE}, 2fr)`,
                gridTemplateRows: `repeat(${GRID_SIZE}, 1fr)`,
                width: '630px',
                height: '600px',
              }}
            >
              {Array.from({ length: GRID_SIZE * GRID_SIZE }).map((_, index) => (
                <div 
                  key={index}
                  className="border border-green-900/30"
                ></div>
              ))}

              {snake.map((segment, index) => (
                <div
                  key={index}
                  className={`absolute ${index === 0 ? 'bg-green-300' : 'bg-green-500'} border border-green-300 transition-all duration-75`}
                  style={{
                    left: `${(segment.x / GRID_SIZE) * 100}%`,
                    top: `${(segment.y / GRID_SIZE) * 100}%`,
                    width: `${100 / GRID_SIZE}%`,
                    height: `${100 / GRID_SIZE}%`,
                  }}
                ></div>
              ))}

              <div
                className="absolute bg-red-500 border border-red-300 animate-pulse"
                style={{
                  left: `${(food.x / GRID_SIZE) * 100}%`,
                  top: `${(food.y / GRID_SIZE) * 100}%`,
                  width: `${100 / GRID_SIZE}%`,
                  height: `${100 / GRID_SIZE}%`,
                }}
              ></div>
            </div>

            {gameOver && (
              <div className="absolute inset-0 bg-black/80 flex items-center justify-center">
                <div className="text-center">
                  <h3 className="text-3xl text-red-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>GAME OVER</h3>
                  <p className="text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>Puntuación Final: {score}</p>
                  <Button onClick={resetGame} className="bg-green-600 hover:bg-green-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                    JUGAR DE NUEVO
                  </Button>
                </div>
              </div>
            )}

            {(!gameRunning && !gameOver && (direction.x !== 0 || direction.y !== 0)) ? (
            <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
              <div className="text-center">
                <h3 className="text-2xl text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>PAUSADO</h3>
                <Button onClick={pauseGame} className="bg-green-600 hover:bg-green-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  CONTINUAR
                </Button>
              </div>
            </div>
          ) : null}
          </div>

          <div className="mt-4 text-center text-green-400 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
            Usa las flechas para moverte • ESC para pausar
          </div>
          </div>
        </Card>
      </div>
    </div>
  );
}