import React, { useState, useEffect, useCallback, useRef } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { ArrowLeft, Play, Pause, RotateCcw } from 'lucide-react';

interface PongGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
}

const CANVAS_WIDTH = 600;
const CANVAS_HEIGHT = 400;

const PADDLE_HEIGHT = 120;
const PADDLE_WIDTH = 12;
const BALL_SIZE = 14;

const WINNING_SCORE = 3;

const AI_SPEEDS = {
  easy: 200,
  normal: 350,
  hard: 500,
};

const BALL_SPEEDS = {
  easy: 250,
  normal: 350,
  hard: 450,
};

export function PongGame({ onBack, onScore }: PongGameProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  const [difficulty, setDifficulty] = useState<'easy' | 'normal' | 'hard'>('normal');

  const [gameRunning, setGameRunning] = useState(false);
  const [gameOver, setGameOver] = useState(false);
  const [playerScore, setPlayerScore] = useState(0);
  const [aiScore, setAiScore] = useState(0);

  const gameState = useRef({
    playerY: CANVAS_HEIGHT / 2 - PADDLE_HEIGHT / 2,
    aiY: CANVAS_HEIGHT / 2 - PADDLE_HEIGHT / 2,
    ballX: CANVAS_WIDTH / 2,
    ballY: CANVAS_HEIGHT / 2,
    ballVelX: 350,
    ballVelY: 210,
  });

  const lastTime = useRef<number>(0);
  const animationFrameId = useRef<number | null>(null);

  const resetBall = (direction: number) => {
    const ballSpeed = BALL_SPEEDS[difficulty];
    gameState.current.ballX = CANVAS_WIDTH / 2;
    gameState.current.ballY = CANVAS_HEIGHT / 2;
    gameState.current.ballVelX = ballSpeed * direction;
    gameState.current.ballVelY = (Math.random() - 0.5) * ballSpeed;
  };

  const draw = useCallback(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const state = gameState.current;

    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    ctx.fillStyle = 'white';
    ctx.fillRect(0, state.playerY, PADDLE_WIDTH, PADDLE_HEIGHT);
    ctx.fillRect(CANVAS_WIDTH - PADDLE_WIDTH, state.aiY, PADDLE_WIDTH, PADDLE_HEIGHT);

    ctx.beginPath();
    ctx.setLineDash([10, 10]);
    ctx.moveTo(CANVAS_WIDTH / 2, 0);
    ctx.lineTo(CANVAS_WIDTH / 2, CANVAS_HEIGHT);
    ctx.strokeStyle = 'white';
    ctx.stroke();
    ctx.setLineDash([]);

    ctx.beginPath();
    ctx.arc(state.ballX, state.ballY, BALL_SIZE / 2, 0, Math.PI * 2);
    ctx.fill();
  }, []);

  const gameLoop = useRef<(currentTime: number) => void>();

  useEffect(() => {
  gameLoop.current = (currentTime: number) => {
    if (!gameRunning) return;

    if (lastTime.current === 0) {
      lastTime.current = currentTime;
      animationFrameId.current = requestAnimationFrame(gameLoop.current!);
      return;
    }

    const deltaTime = (currentTime - lastTime.current) / 1000;
    lastTime.current = currentTime;

    const state = gameState.current;
    const aiSpeed = AI_SPEEDS[difficulty];

    let targetY = state.ballY;
    const aiCenter = state.aiY + PADDLE_HEIGHT / 2;

    // Ajustar precisión según dificultad
    const aiAccuracy = {
      easy: 0.7,    // 70% de precisión - más errático
      normal: 0.85, // 85% de precisión
      hard: 0.98,   // 98% de precisión - casi perfecto
    };

    const accuracy = aiAccuracy[difficulty];
    
    // Añadir error aleatorio según la precisión
    if (Math.random() > accuracy) {
      targetY += (Math.random() - 0.5) * PADDLE_HEIGHT * 0.8;
    }

    targetY = Math.max(0, Math.min(targetY, CANVAS_HEIGHT));

    const dy = targetY - aiCenter;
    if (Math.abs(dy) > 5) {
      state.aiY += Math.sign(dy) * aiSpeed * deltaTime;
      state.aiY = Math.max(0, Math.min(state.aiY, CANVAS_HEIGHT - PADDLE_HEIGHT));
    }

    state.ballX += state.ballVelX * deltaTime;
    state.ballY += state.ballVelY * deltaTime;

    if (state.ballY - BALL_SIZE / 2 <= 0 || state.ballY + BALL_SIZE / 2 >= CANVAS_HEIGHT) {
      state.ballVelY = -state.ballVelY;
    }

    if (
      state.ballX - BALL_SIZE / 2 <= PADDLE_WIDTH &&
      state.ballY + BALL_SIZE / 2 >= state.playerY &&
      state.ballY - BALL_SIZE / 2 <= state.playerY + PADDLE_HEIGHT
    ) {
      state.ballVelX = Math.abs(state.ballVelX) * 1.05;
      const relativeIntersectY = (state.playerY + PADDLE_HEIGHT / 2) - state.ballY;
      state.ballVelY = -relativeIntersectY * 0.12 * (60 / (PADDLE_HEIGHT / 2));
    }

    if (
      state.ballX + BALL_SIZE / 2 >= CANVAS_WIDTH - PADDLE_WIDTH &&
      state.ballY + BALL_SIZE / 2 >= state.aiY &&
      state.ballY - BALL_SIZE / 2 <= state.aiY + PADDLE_HEIGHT
    ) {
      state.ballVelX = -Math.abs(state.ballVelX) * 1.05;
      const relativeIntersectY = (state.aiY + PADDLE_HEIGHT / 2) - state.ballY;
      state.ballVelY = -relativeIntersectY * 0.12 * (60 / (PADDLE_HEIGHT / 2));
    }

    if (state.ballX < 0) {
      setAiScore(prev => {
        const newScore = prev + 1;
        if (newScore >= WINNING_SCORE) {
          setGameOver(true);
          setGameRunning(false);
          onScore(0);
        }
        return newScore;
      });
      resetBall(1);
    }

    if (state.ballX > CANVAS_WIDTH) {
      setPlayerScore(prev => {
        const newScore = prev + 1;
        if (newScore >= WINNING_SCORE) {
          setGameOver(true);
          setGameRunning(false);
          onScore(1);
        }
        return newScore;
      });
      resetBall(-1);
    }

    draw();
    animationFrameId.current = requestAnimationFrame(gameLoop.current!);
  };
}, [difficulty, gameRunning, onScore, draw]);


  const resetGame = useCallback(() => {
    if (animationFrameId.current) cancelAnimationFrame(animationFrameId.current);

    setPlayerScore(0);
    setAiScore(0);
    setGameRunning(false);
    setGameOver(false);
    lastTime.current = 0;

    const ballSpeed = BALL_SPEEDS[difficulty];
    gameState.current = {
      playerY: CANVAS_HEIGHT / 2 - PADDLE_HEIGHT / 2,
      aiY: CANVAS_HEIGHT / 2 - PADDLE_HEIGHT / 2,
      ballX: CANVAS_WIDTH / 2,
      ballY: CANVAS_HEIGHT / 2,
      ballVelX: ballSpeed,
      ballVelY: ballSpeed * 0.6,
    };

    draw();
  }, [draw, difficulty]);

  const startGame = () => {
    if (gameOver) resetGame();
    lastTime.current = performance.now();
    setGameRunning(true);
  };

  const pauseGame = () => setGameRunning(prev => !prev);

  useEffect(() => {
    if (gameRunning) {
      lastTime.current = 0;
      animationFrameId.current = requestAnimationFrame(gameLoop.current!);
    } else if (animationFrameId.current) {
      cancelAnimationFrame(animationFrameId.current);
    }

    return () => {
      if (animationFrameId.current) cancelAnimationFrame(animationFrameId.current);
    };
  }, [gameRunning]);

  useEffect(() => {
    draw();
  }, [draw]);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const handleMouseMove = (e: MouseEvent) => {
      if (!gameRunning) return;
      const rect = canvas.getBoundingClientRect();
      const mouseY = e.clientY - rect.top;
      gameState.current.playerY = Math.max(
        0,
        Math.min(mouseY - PADDLE_HEIGHT / 2, CANVAS_HEIGHT - PADDLE_HEIGHT)
      );
    };

    canvas.addEventListener('mousemove', handleMouseMove);
    return () => canvas.removeEventListener('mousemove', handleMouseMove);
  }, [gameRunning]);

  return (
    <div className="min-h-screen p-4 bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900">

      <div className="text-center py-6">
        <h1 className="text-4xl md:text-6xl tracking-wider mb-3 bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500 bg-clip-text text-transparent font-bold">
          PONG
        </h1>

        <div className="flex justify-center gap-6 text-white mt-2 text-xs md:text-sm">

          <label className="cursor-pointer flex items-center gap-2 hover:text-blue-400 transition-colors">
            <input
              type="radio"
              name="difficulty"
              value="easy"
              checked={difficulty === 'easy'}
              onChange={() => {
                setDifficulty('easy');
                if (gameRunning || gameOver) resetGame();
              }}
              className="accent-blue-400"
            />
            Fácil
          </label>

          <label className="cursor-pointer flex items-center gap-2 hover:text-blue-400 transition-colors">
            <input
              type="radio"
              name="difficulty"
              value="normal"
              checked={difficulty === 'normal'}
              onChange={() => {
                setDifficulty('normal');
                if (gameRunning || gameOver) resetGame();
              }}
              className="accent-blue-400"
            />
            Normal
          </label>

          <label className="cursor-pointer flex items-center gap-2 hover:text-blue-400 transition-colors">
            <input
              type="radio"
              name="difficulty"
              value="hard"
              checked={difficulty === 'hard'}
              onChange={() => {
                setDifficulty('hard');
                if (gameRunning || gameOver) resetGame();
              }}
              className="accent-blue-400"
            />
            Difícil
          </label>

        </div>

        <div className="w-24 h-1 bg-gradient-to-r from-blue-400 to-cyan-400 mx-auto mt-3"></div>
      </div>

      <div className="flex items-center justify-center min-h-[70vh]">
        <Card className="bg-gray-900/90 border-blue-500 border-2 shadow-2xl shadow-blue-500/50 backdrop-blur-sm">
          <div className="p-6">

            <div className="flex items-center justify-between mb-6">

              <Button
                onClick={onBack}
                variant="outline"
                size="sm"
                className="bg-red-600 hover:bg-red-700 border-red-500 text-white text-xs"
              >
                <ArrowLeft className="w-4 h-4 mr-2" />
                VOLVER
              </Button>

              <div className="text-center">
                <div className="text-white text-sm md:text-base font-bold">
                  <span className="text-cyan-400">TÚ: {playerScore}</span>
                  <span className="mx-4">|</span>
                  <span className="text-yellow-400">CPU: {aiScore}</span>
                </div>
              </div>

              <div className="flex gap-2">
                {!gameRunning && !gameOver && (
                  <Button onClick={startGame} size="sm" className="bg-blue-600 hover:bg-blue-700">
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

            <div className="relative flex justify-center items-center">
              <canvas
                ref={canvasRef}
                width={CANVAS_WIDTH}
                height={CANVAS_HEIGHT}
                className="border-2 border-blue-500 bg-black cursor-none shadow-lg shadow-blue-500/30"
              />

              {gameOver && (
                <div className="absolute inset-0 bg-black/80 flex items-center justify-center">
                  <div className="text-center p-6">
                    <h3 className={`text-2xl md:text-3xl mb-4 font-bold ${playerScore >= WINNING_SCORE ? 'text-green-400' : 'text-red-400'
                      }`}>
                      {playerScore >= WINNING_SCORE ? '¡GANASTE!' : 'PERDISTE'}
                    </h3>

                    <p className="text-yellow-400 mb-4 text-sm md:text-base font-semibold">
                      Puntuación Final: {playerScore} - {aiScore}
                    </p>

                    <Button
                      onClick={resetGame}
                      className="bg-blue-600 hover:bg-blue-700 text-xs md:text-sm font-bold"
                    >
                      JUGAR DE NUEVO
                    </Button>
                  </div>
                </div>
              )}

              {!gameRunning && !gameOver && (playerScore > 0 || aiScore > 0) && (
                <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                  <div className="text-center p-6">
                    <h3 className="text-xl md:text-2xl text-yellow-400 mb-4 font-bold">
                      PAUSADO
                    </h3>
                    <Button
                      onClick={pauseGame}
                      className="bg-blue-600 hover:bg-blue-700 text-xs md:text-sm font-bold"
                    >
                      CONTINUAR
                    </Button>
                  </div>
                </div>
              )}

              {!gameRunning && !gameOver && playerScore === 0 && aiScore === 0 && (
                <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                  <div className="text-center p-6">
                    <h3 className="text-xl md:text-2xl text-cyan-400 mb-4 font-bold">
                      ¡LISTO PARA JUGAR!
                    </h3>
                    <p className="text-white mb-4 text-sm">
                      Mueve el ratón para controlar tu pala
                    </p>
                    <Button
                      onClick={startGame}
                      className="bg-green-600 hover:bg-green-700 text-xs md:text-sm font-bold"
                    >
                      <Play className="w-4 h-4 mr-2" />
                      COMENZAR
                    </Button>
                  </div>
                </div>
              )}

            </div>

            <div className="mt-4 text-center text-blue-400 text-xs md:text-sm">
              Mueve el ratón para controlar la pala • Cambia dificultad en cualquier momento
            </div>

          </div>
        </Card>
      </div>
    </div>
  );
}