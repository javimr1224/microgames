import React, { useState, useEffect, useCallback, useRef } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { ArrowLeft, Play, Pause, RotateCcw } from 'lucide-react';

interface BreakoutGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
}

const CANVAS_WIDTH = 1000;
const CANVAS_HEIGHT = 400;
const PADDLE_WIDTH = 80;
const PADDLE_HEIGHT = 12;
const BALL_SIZE = 8;

const BRICK_ROWS = 9;
const BRICK_COLS = 15;

const BRICK_WIDTH = CANVAS_WIDTH / BRICK_COLS;
const BRICK_HEIGHT = 20;

interface Ball {
  x: number;
  y: number;
  velX: number;
  velY: number;
}

interface Brick {
  x: number;
  y: number;
  visible: boolean;
  color: string;
  width: number;
  height: number;
}

type PowerUpType = 'BiggerPaddle' | 'SlowBall' | 'MultiBall';

interface PowerUp {
  x: number;
  y: number;
  type: PowerUpType;
  width: number;
  height: number;
  velY: number;
  visible: boolean;
}

function createBricks(): Brick[] {
  const initialBricks: Brick[] = [];
  const colors = ['#ff0000', '#ff8800', '#ffff00', '#00ff00', '#0088ff'];
  
  for (let row = 0; row < BRICK_ROWS; row++) {
    for (let col = 0; col < BRICK_COLS; col++) {
      initialBricks.push({
        x: col * BRICK_WIDTH,
        y: row * BRICK_HEIGHT + 30,
        visible: true,
        color: colors[row % colors.length],
        width: BRICK_WIDTH,
        height: BRICK_HEIGHT,
      });
    }
  }
  return initialBricks;
}

interface Rect {
  x: number;
  y: number;
  width: number;
  height: number;
}

const checkCollision = (rect1: Rect, rect2: Rect) => {
  return (
    rect1.x < rect2.x + rect2.width &&
    rect1.x + rect1.width > rect2.x &&
    rect1.y < rect2.y + rect2.height &&
    rect1.y + rect1.height > rect2.y
  );
};


export function BreakoutGame({ onBack, onScore }: BreakoutGameProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  
  const [gameRunning, setGameRunning] = useState(false);
  const [gameOver, setGameOver] = useState(false);
  const [gameWon, setGameWon] = useState(false); 
  const [score, setScore] = useState(0);
  const [lives, setLives] = useState(3);
  const [paddleWidth, setPaddleWidth] = useState(PADDLE_WIDTH);

  const paddleX = useRef(CANVAS_WIDTH / 2 - PADDLE_WIDTH / 2);
  const balls = useRef<Ball[]>([]);
  const bricks = useRef<Brick[]>(createBricks());
  const powerUps = useRef<PowerUp[]>([]);
  const animationFrameId = useRef<number | null>(null);
  const slowBallTimeoutId = useRef<NodeJS.Timeout | null>(null);
  const lastTime = useRef<number>(0);

  const drawGame = useCallback(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    bricks.current.forEach(brick => {
      if (brick.visible) {
        ctx.fillStyle = brick.color;
        ctx.fillRect(brick.x, brick.y, brick.width, brick.height);
        ctx.strokeStyle = '#ffffff';
        ctx.strokeRect(brick.x, brick.y, brick.width, brick.height);
      }
    });

    powerUps.current.forEach(powerUp => {
      if (powerUp.visible) {
        ctx.fillStyle = '#00ffff';
        ctx.fillRect(powerUp.x, powerUp.y, powerUp.width, powerUp.height);
      }
    });

    ctx.fillStyle = '#ff8800';
    ctx.fillRect(paddleX.current, CANVAS_HEIGHT - PADDLE_HEIGHT - 10, paddleWidth, PADDLE_HEIGHT);

    ctx.fillStyle = 'white';
    balls.current.forEach(ball => {
      ctx.fillRect(ball.x, ball.y, BALL_SIZE, BALL_SIZE);
    });
  }, [paddleWidth]);

  const gameLoop = useRef<() => void>();

  useEffect(() => {
    gameLoop.current = (currentTime: number) => {
      if (lastTime.current === 0) {
        lastTime.current = currentTime;
        animationFrameId.current = requestAnimationFrame(gameLoop.current!);
        return;
      }

      const deltaTime = (currentTime - lastTime.current) / 1000;
      lastTime.current = currentTime;

      const activatePowerUp = (type: PowerUpType) => {
        switch (type) {
          case 'BiggerPaddle':
            setPaddleWidth(PADDLE_WIDTH * 1.5);
            setTimeout(() => {
              setPaddleWidth(PADDLE_WIDTH);
            }, 10000); // 10 seconds
            break;
          case 'SlowBall':
            if (slowBallTimeoutId.current) {
              clearTimeout(slowBallTimeoutId.current);
            }
            balls.current.forEach(ball => {
              ball.velX *= 0.8;
              ball.velY *= 0.8;
            });
            slowBallTimeoutId.current = setTimeout(() => {
              balls.current.forEach(ball => {
                ball.velX /= 0.8;
                ball.velY /= 0.8;
              });
            }, 5000); // 5 seconds
            break;
          case 'MultiBall':
            balls.current.push({
              x: paddleX.current + paddleWidth / 2,
              y: CANVAS_HEIGHT - PADDLE_HEIGHT - 20,
              velX: (Math.random() - 0.5) * 480,
              velY: -240,
            });
            break;
        }
      };

      if (!gameRunning || gameOver || gameWon) {
          if (animationFrameId.current) {
              cancelAnimationFrame(animationFrameId.current);
              animationFrameId.current = null;
          }
          return;
      }

      balls.current.forEach((ball, index) => {
        ball.x += ball.velX * deltaTime;
        ball.y += ball.velY * deltaTime;

        if (ball.x <= 0 || ball.x >= CANVAS_WIDTH - BALL_SIZE) {
          ball.velX = -ball.velX;
          ball.x = ball.x <= 0 ? 0 : CANVAS_WIDTH - BALL_SIZE;
        }
        if (ball.y <= 0) {
          ball.velY = -ball.velY;
          ball.y = 0;
        }

        if (
          ball.y + BALL_SIZE >= CANVAS_HEIGHT - PADDLE_HEIGHT - 10 &&
          ball.y <= CANVAS_HEIGHT - PADDLE_HEIGHT - 10 &&
          ball.x + BALL_SIZE >= paddleX.current &&
          ball.x <= paddleX.current + paddleWidth
        ) {
          ball.velY = -Math.abs(ball.velY);
          ball.y = CANVAS_HEIGHT - PADDLE_HEIGHT - 10 - BALL_SIZE;
          const hitPos = ((ball.x + BALL_SIZE / 2) - (paddleX.current + paddleWidth / 2)) / (paddleWidth / 2);
          ball.velX += hitPos * 60;
          ball.velX = Math.max(-360, Math.min(360, ball.velX));
        }

        if (ball.y > CANVAS_HEIGHT) {
          if (balls.current.length === 1) {
            setLives(prev => {
              const newLives = prev - 1;
              if (newLives <= 0) {
                setGameOver(true);
                setGameRunning(false);
                setTimeout(() => onScore(score), 0);
              }
              return newLives;
            });
            ball.x = CANVAS_WIDTH / 2;
            ball.y = CANVAS_HEIGHT - 50;
            ball.velX = Math.random() > 0.5 ? 240 : -240;
            ball.velY = -240;
          } else {
            balls.current.splice(index, 1);
          }
        }

        let brickHit = false;
        bricks.current.forEach(brick => {
          if (!brick.visible || brickHit) return;

          if (checkCollision(
            { x: ball.x, y: ball.y, width: BALL_SIZE, height: BALL_SIZE },
            brick
          )) {
            brickHit = true;
            const ballCenterX = ball.x + BALL_SIZE / 2;
            const ballCenterY = ball.y + BALL_SIZE / 2;
            const brickCenterX = brick.x + BRICK_WIDTH / 2;
            const brickCenterY = brick.y + BRICK_HEIGHT / 2;
            const dx = ballCenterX - brickCenterX;
            const dy = ballCenterY - brickCenterY;

            if (Math.abs(dx / BRICK_WIDTH) > Math.abs(dy / BRICK_HEIGHT)) {
              ball.velX = -ball.velX;
            } else {
              ball.velY = -ball.velY;
            }

            setScore(s => s + 10);
            brick.visible = false;

            if (Math.random() < 0.2) { // 20% chance to spawn a power-up
              const powerUpTypes: PowerUpType[] = ['BiggerPaddle', 'SlowBall', 'MultiBall'];
              const randomType = powerUpTypes[Math.floor(Math.random() * powerUpTypes.length)];
              powerUps.current.push({
                x: brick.x + BRICK_WIDTH / 2 - 10,
                y: brick.y + BRICK_HEIGHT / 2,
                type: randomType,
                width: 20,
                height: 20,
                velY: 120,
                visible: true,
              });
            }
          }
        });
      });

      powerUps.current.forEach(powerUp => {
        if (powerUp.visible) {
          powerUp.y += powerUp.velY * deltaTime;

          if (checkCollision(
            { x: paddleX.current, y: CANVAS_HEIGHT - PADDLE_HEIGHT - 10, width: paddleWidth, height: PADDLE_HEIGHT },
            powerUp
          )) {
            powerUp.visible = false;
            activatePowerUp(powerUp.type);
          }

          if (powerUp.y > CANVAS_HEIGHT) {
            powerUp.visible = false;
          }
        }
      });

      const visibleBricks = bricks.current.filter(brick => brick.visible).length;
      if (visibleBricks === 0) {
        setScore(s => s + 100);
        bricks.current = createBricks();
        balls.current = [{
          x: CANVAS_WIDTH / 2,
          y: CANVAS_HEIGHT - 50,
          velX: 240,
          velY: -240,
        }];
      }

      drawGame();

      animationFrameId.current = requestAnimationFrame(gameLoop.current!);
    }
  }, [gameRunning, gameOver, gameWon, score, onScore, drawGame, paddleWidth]);

  const resetGame = useCallback(() => {
    if (animationFrameId.current) {
      cancelAnimationFrame(animationFrameId.current);
      animationFrameId.current = null;
    }
    lastTime.current = 0;
    paddleX.current = CANVAS_WIDTH / 2 - PADDLE_WIDTH / 2;
    balls.current = [{
      x: CANVAS_WIDTH / 2,
      y: CANVAS_HEIGHT - 50,
      velX: 240,
      velY: -240,
    }];
    bricks.current = createBricks();
    powerUps.current = [];
    setScore(0);
    setLives(3);
    setGameRunning(false);
    setGameOver(false);
    setGameWon(false);
    setPaddleWidth(PADDLE_WIDTH);
    drawGame();
  }, [drawGame]);

  const startGame = () => {
    if (gameRunning) return;

    if (gameOver || gameWon) {
      resetGame();
    }

    lastTime.current = 0;
    balls.current = [
      {
        x: CANVAS_WIDTH / 2,
        y: CANVAS_HEIGHT - 50,
        velX: 240,
        velY: -240,
      },
    ];

    setGameRunning(true);
  };

  const pauseGame = () => {
    setGameRunning(prev => !prev);
  };

  useEffect(() => {
    if (gameRunning && !gameOver && !gameWon) {
      animationFrameId.current = requestAnimationFrame(gameLoop.current!);
    } else {
      if (animationFrameId.current) {
        cancelAnimationFrame(animationFrameId.current);
        animationFrameId.current = null;
      }
    }

    return () => {
      if (animationFrameId.current) {
        cancelAnimationFrame(animationFrameId.current);
      }
    };
  }, [gameRunning, gameOver, gameWon]);
  
  useEffect(() => {
    drawGame();
  }, [drawGame]);


  useEffect(() => {
    let lastUpdate = 0;
    const throttleDelay = 16;
    
    const handleMouseMove = (e: MouseEvent) => {
      const now = performance.now();
      if (now - lastUpdate < throttleDelay) return;
      lastUpdate = now;
      
      const canvas = canvasRef.current;
      if (!canvas || !gameRunning) return; 

      const rect = canvas.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const scaledX = (x / rect.width) * CANVAS_WIDTH;
      
      paddleX.current = Math.max(0, Math.min(scaledX - PADDLE_WIDTH / 2, CANVAS_WIDTH - PADDLE_WIDTH));
    };

    const canvas = canvasRef.current;
    if (canvas) {
      canvas.addEventListener('mousemove', handleMouseMove, { passive: true });
      return () => canvas.removeEventListener('mousemove', handleMouseMove);
    }
  }, [gameRunning]); 

  useEffect(() => {
    const handleKeyPress = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        e.preventDefault();
        pauseGame();
        return;
      }

      if (!gameRunning) return;

      switch (e.key) {
        case 'ArrowLeft':
          e.preventDefault();
          paddleX.current = Math.max(0, paddleX.current - 20);
          break;
        case 'ArrowRight':
          e.preventDefault();
          paddleX.current = Math.min(CANVAS_WIDTH - PADDLE_WIDTH, paddleX.current + 20);
          break;
      }
    };

    window.addEventListener('keydown', handleKeyPress);
    return () => window.removeEventListener('keydown', handleKeyPress);
  }, [gameRunning]);
  

  return (
    <div className="min-h-screen p-4">
      <div className="text-center py-8">
        <h1 className="text-4xl md:text-6xl tracking-wider mb-4 bg-gradient-to-r from-orange-400 via-red-400 to-orange-500 bg-clip-text text-transparent">
          BREAKOUT
        </h1>
        <div className="w-24 h-1 bg-gradient-to-r from-orange-400 to-red-400 mx-auto"></div>
      </div>

      <div className="flex items-center justify-center">
        <Card className="bg-gray-900/90 border-orange-500 border-2 shadow-2xl shadow-orange-500/50 backdrop-blur-sm">
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
                <div className="flex gap-6 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  <div>
                    <span className="text-orange-300">PUNTOS: </span>
                    <span className="text-yellow-400">{score}</span>
                  </div>
                  <div>
                    <span className="text-orange-300">VIDAS: </span>
                    <span className="text-red-400">{'❤️'.repeat(lives)}</span>
                  </div>
                </div>
              </div>
              
              <div className="flex gap-2">
                {!gameRunning && !gameOver && !gameWon && (
                  <Button onClick={startGame} size="sm" className="bg-orange-600 hover:bg-orange-700">
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
              <canvas
                ref={canvasRef}
                width={CANVAS_WIDTH}
                height={CANVAS_HEIGHT}
                className="border-2 border-orange-500 bg-black cursor-none"
              />

              {gameOver && (
                <div className="absolute inset-0 bg-black/80 flex items-center justify-center">
                  <div className="text-center">
                    <h3 className="text-3xl text-red-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>GAME OVER</h3>
                    <p className="text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>Puntuación Final: {score}</p>
                    <Button onClick={resetGame} className="bg-orange-600 hover:bg-orange-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                      JUGAR DE NUEVO
                    </Button>
                  </div>
                </div>
              )}

              {gameWon && (
                <div className="absolute inset-0 bg-black/80 flex items-center justify-center">
                  <div className="text-center">
                    <h3 className="text-3xl text-green-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>¡GANASTE!</h3>
                    <p className="text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>Puntuación Final: {score}</p>
                    <Button onClick={resetGame} className="bg-orange-600 hover:bg-orange-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                      JUGAR DE NUEVO
                    </Button>
                  </div>
                </div>
              )}

              {!gameRunning && !gameOver && !gameWon && lives > 0 && !(lives === 3 && score === 0) && (
                <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                  <div className="text-center">
                    <h3 className="text-2xl text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>PAUSADO</h3>
                    <Button onClick={pauseGame} className="bg-orange-600 hover:bg-orange-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                      CONTINUAR
                    </Button>
                  </div>
                </div>
              )}
            </div>

            <div className="mt-4 text-center text-orange-400 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
              Mueve el mouse o usa ← → para controlar la paleta • ESC para pausar
            </div>
          </div>
        </Card>
      </div>
    </div>
  );
}