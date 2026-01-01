import { useState, useEffect, useCallback } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { ArrowLeft, Play, Pause, RotateCcw } from 'lucide-react';

interface SnakeGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
  fromMenu?: boolean;
}

interface Position {
  x: number;
  y: number;
}

const GRID_SIZE = 20;
const INITIAL_SNAKE = [{ x: 10, y: 10 }];
const INITIAL_FOOD = { x: 5, y: 5 };

export function SnakeGame({ onBack, onScore, fromMenu }: SnakeGameProps) {
  if (!fromMenu) {
    return (
      <div className="flex flex-col items-center justify-center min-h-screen bg-gray-900 text-white">
        <h1 className="text-2xl mb-4">Acceso no permitido</h1>
        <p>Debes entrar al juego desde el menú.</p>
        <button
          onClick={onBack}
          className="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded"
        >
          Volver
        </button>
      </div>
    );
  }

  const [snake, setSnake] = useState<Position[]>(INITIAL_SNAKE);
  const [food, setFood] = useState<Position>(INITIAL_FOOD);
  const [direction, setDirection] = useState<Position>({ x: 0, y: 0 });
  const [gameRunning, setGameRunning] = useState(false);
  const [gameOver, setGameOver] = useState(false);
  const [score, setScore] = useState(0);
  
  // State for swipe controls
  const [touchStart, setTouchStart] = useState<Position | null>(null);
  const [touchEnd, setTouchEnd] = useState<Position | null>(null);
  const minSwipeDistance = 30;

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

  const handleDirectionChange = useCallback((newDirection: Position) => {
    if (!gameRunning) return;

    const isOppositeDirection = 
      (newDirection.x !== 0 && newDirection.x === -direction.x) ||
      (newDirection.y !== 0 && newDirection.y === -direction.y);

    if (!isOppositeDirection) {
      setDirection(newDirection);
    }
  }, [gameRunning, direction]);

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
      e.preventDefault();
      switch (e.key) {
        case 'ArrowUp':
          handleDirectionChange({ x: 0, y: -1 });
          break;
        case 'ArrowDown':
          handleDirectionChange({ x: 0, y: 1 });
          break;
        case 'ArrowLeft':
          handleDirectionChange({ x: -1, y: 0 });
          break;
        case 'ArrowRight':
          handleDirectionChange({ x: 1, y: 0 });
          break;
        case 'Escape':
          pauseGame();
          break;
      }
    };

    window.addEventListener('keydown', handleKeyPress);
    return () => window.removeEventListener('keydown', handleKeyPress);
  }, [handleDirectionChange, pauseGame]);

  // Touch event handlers for swipe controls
  const handleTouchStart = (e: React.TouchEvent) => {
    setTouchEnd(null);
    setTouchStart({ x: e.targetTouches[0].clientX, y: e.targetTouches[0].clientY });
  };

  const handleTouchMove = (e: React.TouchEvent) => {
    setTouchEnd({ x: e.targetTouches[0].clientX, y: e.targetTouches[0].clientY });
  };

  const handleTouchEnd = () => {
    if (!touchStart || !touchEnd) return;
    const diffX = touchEnd.x - touchStart.x;
    const diffY = touchEnd.y - touchStart.y;

    if (Math.abs(diffX) > Math.abs(diffY)) {
      if (Math.abs(diffX) > minSwipeDistance) {
        if (diffX > 0) handleDirectionChange({ x: 1, y: 0 }); // Right
        else handleDirectionChange({ x: -1, y: 0 }); // Left
      }
    } else {
      if (Math.abs(diffY) > minSwipeDistance) {
        if (diffY > 0) handleDirectionChange({ x: 0, y: 1 }); // Down
        else handleDirectionChange({ x: 0, y: -1 }); // Up
      }
    }
    setTouchStart(null);
    setTouchEnd(null);
  };

  return (
    <div className="min-h-screen p-4 flex flex-col items-center">
      <div className="text-center py-8 w-full">
        <h1 className="text-4xl md:text-6xl tracking-wider mb-4 bg-gradient-to-r from-green-400 via-emerald-400 to-green-500 bg-clip-text text-transparent">
          SNAKE
        </h1>
        <div className="w-24 h-1 bg-gradient-to-r from-green-400 to-emerald-400 mx-auto"></div>
      </div>

      <div className="flex items-center justify-center w-full">
        <Card className="bg-gray-900/90 border-green-500 border-2 shadow-2xl shadow-green-500/50 backdrop-blur-sm w-full max-w-[630px]">
          <div className="p-4 sm:p-6">
            <div className="flex items-center justify-between mb-4 sm:mb-6">
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
                <div className="text-yellow-400 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>PUNTOS: {score}</div>
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

          <div 
            className="relative w-full aspect-square"
            style={{ touchAction: 'none' }}
            onTouchStart={handleTouchStart}
            onTouchMove={handleTouchMove}
            onTouchEnd={handleTouchEnd}
          >
            <div 
              className="grid bg-black border-2 border-green-500 w-full h-full"
              style={{
                gridTemplateColumns: `repeat(${GRID_SIZE}, 1fr)`,
                gridTemplateRows: `repeat(${GRID_SIZE}, 1fr)`,
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
                  className={`absolute ${index === 0 ? 'bg-green-300' : 'bg-green-500'} border border-green-300 transition-transform duration-75`}
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
                <div className="text-center p-4">
                  <h3 className="text-2xl sm:text-3xl text-red-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>GAME OVER</h3>
                  <p className="text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>Puntuación Final: {score}</p>
                  <Button onClick={resetGame} className="bg-green-600 hover:bg-green-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                    JUGAR DE NUEVO
                  </Button>
                </div>
              </div>
            )}

            {(!gameRunning && !gameOver && (direction.x !== 0 || direction.y !== 0)) ? (
            <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
              <div className="text-center p-4">
                <h3 className="text-xl sm:text-2xl text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>PAUSADO</h3>
                <Button onClick={pauseGame} className="bg-green-600 hover:bg-green-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  CONTINUAR
                </Button>
              </div>
            </div>
          ) : null}
          </div>

          <div className="mt-4 text-center text-green-400 text-xs sm:text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
            <span className="hidden md:block">Usa las flechas para moverte • ESC para pausar</span>
            <span className="md:hidden">Desliza para moverte</span>
          </div>
          </div>
        </Card>
      </div>
    </div>
  );
}