import { useState, useEffect, useCallback, useRef } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { ArrowLeft, Play, Pause, RotateCcw, Save } from 'lucide-react';

interface TetrisGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
  fromMenu?: boolean;
}

const BOARD_WIDTH = 10;
const BOARD_HEIGHT = 20;
const CELL_SIZE = 20;

const TETROMINOS = {
  I: { shape: [[1, 1, 1, 1]], color: '#00f5ff' },
  O: { shape: [[1, 1], [1, 1]], color: '#ffff00' },
  T: { shape: [[0, 1, 0], [1, 1, 1]], color: '#800080' },
  S: { shape: [[0, 1, 1], [1, 1, 0]], color: '#00ff00' },
  Z: { shape: [[1, 1, 0], [0, 1, 1]], color: '#ff0000' },
  J: { shape: [[1, 0, 0], [1, 1, 1]], color: '#0000ff' },
  L: { shape: [[0, 0, 1], [1, 1, 1]], color: '#ffa500' },
};

type TetrominoType = keyof typeof TETROMINOS;

interface Piece {
  shape: number[][];
  color: string;
  x: number;
  y: number;
}

export function TetrisGame({ onBack, onScore, fromMenu }: TetrisGameProps) {
  if (!fromMenu) {
    return (
      <div className="flex flex-col items-center justify-center min-h-screen bg-gray-900 text-white">
        <h1 className="text-2xl mb-4">Acceso no permitido</h1>
        <p>Debes entrar al juego desde el menú.</p>
        <button onClick={onBack} className="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded">Volver</button>
      </div>
    );
  }

  const canvasRef = useRef<HTMLCanvasElement>(null);
  const nextPieceCanvasRef = useRef<HTMLCanvasElement>(null);
  const heldPieceCanvasRef = useRef<HTMLCanvasElement>(null);

  const [board, setBoard] = useState<string[][]>(() => Array(BOARD_HEIGHT).fill(null).map(() => Array(BOARD_WIDTH).fill('')));
  const [currentPiece, setCurrentPiece] = useState<Piece | null>(null);
  const [nextPiece, setNextPiece] = useState<Piece | null>(null);
  const [heldPiece, setHeldPiece] = useState<Piece | null>(null);
  const hasSwapped = useRef(false);

  const [gameRunning, setGameRunning] = useState(false);
  const [gameOver, setGameOver] = useState(false);
  const [score, setScore] = useState(0);
  const [level, setLevel] = useState(1);
  const [linesCleared, setLinesCleared] = useState(0);

  // Touch state
  const touchState = useRef({
    startPos: { x: 0, y: 0 },
    startTime: 0,
    pieceStartPos: { x: 0, y: 0 },
  });

  const createRandomPiece = useCallback((): Piece => {
    const types = Object.keys(TETROMINOS) as TetrominoType[];
    const randomType = types[Math.floor(Math.random() * types.length)];
    const tetromino = TETROMINOS[randomType];
    return {
      shape: tetromino.shape,
      color: tetromino.color,
      x: Math.floor(BOARD_WIDTH / 2) - Math.floor(tetromino.shape[0].length / 2),
      y: 0,
    };
  }, []);

  const isValidPosition = useCallback((piece: Piece, boardState: string[][]): boolean => {
    for (let y = 0; y < piece.shape.length; y++) {
      for (let x = 0; x < piece.shape[y].length; x++) {
        if (piece.shape[y][x]) {
          const newX = piece.x + x;
          const newY = piece.y + y;
          if (newX < 0 || newX >= BOARD_WIDTH || newY >= BOARD_HEIGHT || (newY >= 0 && boardState[newY][newX])) {
            return false;
          }
        }
      }
    }
    return true;
  }, []);

  const placePiece = useCallback((piece: Piece, boardState: string[][]): string[][] => {
    const newBoard = boardState.map(row => [...row]);
    piece.shape.forEach((row, y) => {
      row.forEach((cell, x) => {
        if (cell) {
          const boardY = piece.y + y;
          const boardX = piece.x + x;
          if (boardY >= 0) newBoard[boardY][boardX] = piece.color;
        }
      });
    });
    return newBoard;
  }, []);

  const clearLines = useCallback((boardState: string[][]): { newBoard: string[][]; clearedLines: number } => {
    const newBoard: string[][] = [];
    let clearedLines = 0;
    boardState.forEach(row => {
      if (row.every(cell => cell !== '')) {
        clearedLines++;
      } else {
        newBoard.push(row);
      }
    });
    while (newBoard.length < BOARD_HEIGHT) {
      newBoard.unshift(Array(BOARD_WIDTH).fill(''));
    }
    return { newBoard, clearedLines };
  }, []);

  const resetGame = useCallback(() => {
    setBoard(Array(BOARD_HEIGHT).fill(null).map(() => Array(BOARD_WIDTH).fill('')));
    setCurrentPiece(null);
    setNextPiece(null);
    setHeldPiece(null);
    hasSwapped.current = false;
    setGameRunning(false);
    setGameOver(false);
    setScore(0);
    setLevel(1);
    setLinesCleared(0);
  }, []);

  const startGame = () => {
    resetGame();
    setGameRunning(true);
    setCurrentPiece(createRandomPiece());
    setNextPiece(createRandomPiece());
  };

  const pauseGame = useCallback(() => setGameRunning(prev => !prev), []);

  const advanceGame = useCallback((placedPiece: Piece) => {
    hasSwapped.current = false;
    const newBoard = placePiece(placedPiece, board);
    const { newBoard: clearedBoard, clearedLines: lines } = clearLines(newBoard);

    setBoard(clearedBoard);
    if (lines > 0) {
      const linesPoints = [0, 100, 300, 500, 800][lines] * level;
      setScore(prev => prev + linesPoints);
      setLinesCleared(prev => {
        const totalLines = prev + lines;
        setLevel(Math.floor(totalLines / 10) + 1);
        return totalLines;
      });
    }

    const newCurrent = nextPiece;
    const newNext = createRandomPiece();
    if (newCurrent && !isValidPosition(newCurrent, clearedBoard)) {
      setGameOver(true);
      setGameRunning(false);
      onScore(score);
    } else {
      setCurrentPiece(newCurrent);
      setNextPiece(newNext);
    }
  }, [board, clearLines, createRandomPiece, isValidPosition, level, nextPiece, onScore, placePiece, score]);

  const gameLoop = useCallback(() => {
    if (!gameRunning || gameOver || !currentPiece) return;
    const newPiece = { ...currentPiece, y: currentPiece.y + 1 };
    if (isValidPosition(newPiece, board)) {
      setCurrentPiece(newPiece);
    } else {
      advanceGame(currentPiece);
    }
  }, [gameRunning, gameOver, currentPiece, board, isValidPosition, advanceGame]);

  const movePiece = useCallback((dx: number) => {
    if (!gameRunning || !currentPiece) return;
    const newPiece = { ...currentPiece, x: currentPiece.x + dx };
    if (isValidPosition(newPiece, board)) {
      setCurrentPiece(newPiece);
    }
  }, [gameRunning, currentPiece, board, isValidPosition]);

  const rotate = useCallback(() => {
    if (!gameRunning || !currentPiece) return;
    const rotated = { ...currentPiece, shape: currentPiece.shape[0].map((_, i) => currentPiece.shape.map(row => row[i]).reverse()) };
    
    // Wall kick logic
    let kickPiece = rotated;
    const kicks = [0, 1, -1, 2, -2];
    for (const kick of kicks) {
        kickPiece = { ...rotated, x: rotated.x + kick };
        if (isValidPosition(kickPiece, board)) {
            setCurrentPiece(kickPiece);
            return;
        }
    }
  }, [gameRunning, currentPiece, board, isValidPosition]);

  const softDrop = useCallback(() => {
    if (!gameRunning || !currentPiece) return;
    const newPiece = { ...currentPiece, y: currentPiece.y + 1 };
    if (isValidPosition(newPiece, board)) {
      setCurrentPiece(newPiece);
      setScore(prev => prev + 1);
    }
  }, [gameRunning, currentPiece, board, isValidPosition]);

  const hardDrop = useCallback(() => {
    if (!gameRunning || !currentPiece) return;
    let dropPiece = { ...currentPiece };
    while (isValidPosition({ ...dropPiece, y: dropPiece.y + 1 }, board)) {
      dropPiece.y += 1;
    }
    setScore(prev => prev + (dropPiece.y - currentPiece.y) * 2);
    advanceGame(dropPiece);
  }, [gameRunning, currentPiece, board, isValidPosition, advanceGame]);

  const swapPiece = useCallback(() => {
    if (!gameRunning || hasSwapped.current) return;
    hasSwapped.current = true;
    if (!heldPiece) {
      setHeldPiece(currentPiece);
      setCurrentPiece(nextPiece);
      setNextPiece(createRandomPiece());
    } else {
      const newCurrent = { ...heldPiece, x: currentPiece?.x ?? 0, y: currentPiece?.y ?? 0 };
      if (isValidPosition(newCurrent, board)) {
        setHeldPiece(currentPiece);
        setCurrentPiece(newCurrent);
      } else {
        hasSwapped.current = false;
      }
    }
  }, [gameRunning, currentPiece, heldPiece, nextPiece, board, isValidPosition, createRandomPiece]);

  useEffect(() => {
    const handleKeyPress = (e: KeyboardEvent) => {
      if (!gameRunning || gameOver) {
        if (e.key === 'Escape') pauseGame();
        return;
      }
      e.preventDefault();
      switch (e.key) {
        case 'ArrowLeft': movePiece(-1); break;
        case 'ArrowRight': movePiece(1); break;
        case 'ArrowDown': softDrop(); break;
        case 'ArrowUp': rotate(); break;
        case ' ': hardDrop(); break;
        case 'c': swapPiece(); break;
        case 'Escape': pauseGame(); break;
      }
    };
    window.addEventListener('keydown', handleKeyPress);
    return () => window.removeEventListener('keydown', handleKeyPress);
  }, [gameRunning, gameOver, movePiece, softDrop, rotate, hardDrop, swapPiece, pauseGame]);

  useEffect(() => {
    if (!gameRunning) return;
    const speed = Math.max(50, 500 - (level - 1) * 50);
    const interval = setInterval(gameLoop, speed);
    return () => clearInterval(interval);
  }, [gameLoop, level, gameRunning]);
  
  // Touch Handlers
  const handleTouchStart = (e: React.TouchEvent<HTMLCanvasElement>) => {
    if (!gameRunning || !currentPiece) return;
    const touch = e.touches[0];
    touchState.current = {
      startPos: { x: touch.clientX, y: touch.clientY },
      startTime: Date.now(),
      pieceStartPos: { x: currentPiece.x, y: currentPiece.y },
    };
  };

  const handleTouchMove = (e: React.TouchEvent<HTMLCanvasElement>) => {
    if (!gameRunning || !currentPiece) return;
    const touch = e.touches[0];
    const { startPos, pieceStartPos } = touchState.current;
    
    const deltaX = touch.clientX - startPos.x;
    const deltaY = touch.clientY - startPos.y;

    // Horizontal Movement
    const newX = pieceStartPos.x + Math.round(deltaX / (CELL_SIZE * 1.5));
    if (newX !== currentPiece.x) {
      const newPiece = { ...currentPiece, x: newX };
      if (isValidPosition(newPiece, board)) {
        setCurrentPiece(newPiece);
      }
    }
    
    // Soft Drop
    if (deltaY > CELL_SIZE) {
      softDrop();
      touchState.current.startPos.y = touch.clientY; // Reset for continuous drop
    }
  };

  const handleTouchEnd = (e: React.TouchEvent<HTMLCanvasElement>) => {
    if (!gameRunning) return;
    const { startPos, startTime } = touchState.current;
    const endPos = e.changedTouches[0];
    const endTime = Date.now();

    const deltaX = endPos.clientX - startPos.x;
    const deltaY = endPos.clientY - startPos.y;
    const duration = endTime - startTime;

    // Tap detection for rotation
    if (duration < 200 && Math.abs(deltaX) < 20 && Math.abs(deltaY) < 20) {
      rotate();
      return;
    }

    // Hard drop detection (fast swipe down)
    const velocityY = deltaY / duration;
    if (deltaY > 100 && velocityY > 0.8) {
      hardDrop();
    }
  };


  const drawPiece = (ctx: CanvasRenderingContext2D, piece: Piece, options: { ghost?: boolean } = {}) => {
    ctx.fillStyle = options.ghost ? piece.color + '50' : piece.color;
    piece.shape.forEach((row, y) => {
      row.forEach((cell, x) => {
        if (cell) {
          const drawX = (piece.x + x) * CELL_SIZE;
          const drawY = (piece.y + y) * CELL_SIZE;
          ctx.fillRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
          if (!options.ghost) {
            ctx.strokeStyle = '#ffffff';
            ctx.strokeRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
          }
        }
      });
    });
  };

  const draw = useCallback(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, BOARD_WIDTH * CELL_SIZE, BOARD_HEIGHT * CELL_SIZE);

    board.forEach((row, y) => {
      row.forEach((cell, x) => {
        if (cell) {
          ctx.fillStyle = cell;
          ctx.fillRect(x * CELL_SIZE, y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
          ctx.strokeStyle = '#ffffff';
          ctx.strokeRect(x * CELL_SIZE, y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
        }
      });
    });

    if (currentPiece) {
      const ghostPiece = { ...currentPiece };
      while (isValidPosition({ ...ghostPiece, y: ghostPiece.y + 1 }, board)) {
        ghostPiece.y++;
      }
      drawPiece(ctx, ghostPiece, { ghost: true });
      drawPiece(ctx, currentPiece);
    }
    
    // Grid lines
    ctx.strokeStyle = '#333333';
    ctx.lineWidth = 1;
    for (let x = 0; x <= BOARD_WIDTH; x++) {
      ctx.beginPath(); ctx.moveTo(x * CELL_SIZE, 0); ctx.lineTo(x * CELL_SIZE, BOARD_HEIGHT * CELL_SIZE); ctx.stroke();
    }
    for (let y = 0; y <= BOARD_HEIGHT; y++) {
      ctx.beginPath(); ctx.moveTo(0, y * CELL_SIZE); ctx.lineTo(BOARD_WIDTH * CELL_SIZE, y * CELL_SIZE); ctx.stroke();
    }
  }, [board, currentPiece, isValidPosition]);

  const drawSmallCanvas = useCallback((canvas: HTMLCanvasElement | null, piece: Piece | null) => {
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    ctx.fillStyle = '#000000';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    if (piece) {
      const offsetX = (canvas.width - piece.shape[0].length * CELL_SIZE) / 2;
      const offsetY = (canvas.height - piece.shape.length * CELL_SIZE) / 2;
      ctx.fillStyle = piece.color;
      piece.shape.forEach((row, y) => {
        row.forEach((cell, x) => {
          if (cell) {
            ctx.fillRect(offsetX + x * CELL_SIZE, offsetY + y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
            ctx.strokeStyle = '#ffffff';
            ctx.strokeRect(offsetX + x * CELL_SIZE, offsetY + y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
          }
        });
      });
    }
  }, []);

  useEffect(() => {
    draw();
    drawSmallCanvas(nextPieceCanvasRef.current, nextPiece);
    drawSmallCanvas(heldPieceCanvasRef.current, heldPiece);
  }, [draw, drawSmallCanvas, nextPiece, heldPiece]);

  return (
    <div className="min-h-screen p-2 sm:p-4">
      <div className="text-center py-4 sm:py-8">
        <h1 className="text-3xl sm:text-4xl md:text-6xl tracking-wider mb-4 bg-gradient-to-r from-purple-400 via-violet-400 to-purple-500 bg-clip-text text-transparent">TETRIS</h1>
        <div className="w-24 h-1 bg-gradient-to-r from-purple-400 to-violet-400 mx-auto"></div>
      </div>

      <div className="flex items-center justify-center">
        <Card className="bg-gray-900/90 border-purple-500 border-2 shadow-2xl shadow-purple-500/50 backdrop-blur-sm w-full max-w-4xl">
          <div className="p-2 sm:p-6">
            <div className="flex flex-col sm:flex-row items-center justify-between mb-4 sm:mb-6">
              <Button onClick={onBack} variant="outline" size="sm" className="bg-red-600 hover:bg-red-700 border-red-500 text-white mb-4 sm:mb-0">
                <ArrowLeft className="w-4 h-4 mr-2" /> VOLVER
              </Button>
              <div className="flex-grow text-center">
                <div className="grid grid-cols-3 gap-2 sm:gap-4 text-xs sm:text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  <div><div className="text-purple-300">PUNTOS</div><div className="text-yellow-400">{score}</div></div>
                  <div><div className="text-purple-300">NIVEL</div><div className="text-green-400">{level}</div></div>
                  <div><div className="text-purple-300">LÍNEAS</div><div className="text-cyan-400">{linesCleared}</div></div>
                </div>
              </div>
              <div className="flex gap-2 mt-4 sm:mt-0">
                <Button onClick={!gameRunning && !gameOver ? startGame : pauseGame} size="sm" className={gameRunning ? "bg-yellow-600 hover:bg-yellow-700" : "bg-purple-600 hover:bg-purple-700"}>
                  {gameRunning ? <Pause className="w-4 h-4" /> : <Play className="w-4 h-4" />}
                </Button>
                <Button onClick={resetGame} size="sm" className="bg-red-600 hover:bg-red-700"><RotateCcw className="w-4 h-4" /></Button>
              </div>
            </div>

            <div className="flex justify-center items-start gap-2 sm:gap-6">
              <div className="flex flex-col gap-4 w-20 sm:w-auto">
                <div className="bg-black border-2 border-purple-500 p-2 sm:p-3 rounded text-center">
                  <div className="text-purple-300 text-[10px] sm:text-xs mb-2" style={{ fontFamily: 'Press Start 2P, monospace' }}>GUARDADA</div>
                  <canvas ref={heldPieceCanvasRef} width={100} height={100} className="bg-black rounded" />
                </div>
                <Button onClick={swapPiece} className="md:hidden bg-purple-600 hover:bg-purple-700 h-16"><Save /></Button>
              </div>

              <div className="relative">
                <canvas
                  ref={canvasRef}
                  width={BOARD_WIDTH * CELL_SIZE}
                  height={BOARD_HEIGHT * CELL_SIZE}
                  className="border-2 border-purple-500 bg-black max-w-full h-auto"
                  onTouchStart={handleTouchStart}
                  onTouchMove={handleTouchMove}
                  onTouchEnd={handleTouchEnd}
                />
                {gameOver && (
                  <div className="absolute inset-0 bg-black/80 flex items-center justify-center">
                    <div className="text-center p-2">
                      <h3 className="text-2xl sm:text-3xl text-red-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>GAME OVER</h3>
                      <p className="text-yellow-400 mb-2 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>Puntuación Final: {score}</p>
                      <p className="text-cyan-400 mb-4 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>Nivel Alcanzado: {level}</p>
                      <Button onClick={startGame} className="bg-purple-600 hover:bg-purple-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>JUGAR DE NUEVO</Button>
                    </div>
                  </div>
                )}
                {!gameRunning && (linesCleared > 0 || heldPiece) && !gameOver && (
                  <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                    <div className="text-center">
                      <h3 className="text-xl sm:text-2xl text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>PAUSADO</h3>
                      <Button onClick={pauseGame} className="bg-purple-600 hover:bg-purple-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>CONTINUAR</Button>
                    </div>
                  </div>
                )}
              </div>

              <div className="flex flex-col gap-4 w-20 sm:w-auto">
                <div className="bg-black border-2 border-purple-500 p-2 sm:p-3 rounded text-center">
                  <div className="text-purple-300 text-[10px] sm:text-xs mb-2" style={{ fontFamily: 'Press Start 2P, monospace' }}>SIGUIENTE</div>
                  <canvas ref={nextPieceCanvasRef} width={100} height={100} className="bg-black rounded" />
                </div>
              </div>
            </div>

            <div className="mt-4 text-center text-purple-400 text-[10px] sm:text-xs" style={{ fontFamily: 'Press Start 2P, monospace' }}>
              <p className="hidden md:block">←→ Mover • ↓ Bajar • ↑ Rotar • ESPACIO Caída Rápida • C Guardar • ESC Pausar</p>
              <p className="md:hidden">Desliza para mover • Toca para rotar • Desliza abajo para bajar</p>
            </div>
          </div>
        </Card>
      </div>
    </div>
  );
}