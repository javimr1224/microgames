import React, { useState, useEffect, useCallback, useRef } from 'react';
import { Button } from './ui/button';
import { Card } from './ui/card';
import { ArrowLeft, Play, Pause, RotateCcw } from 'lucide-react';

interface TetrisGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
}

const BOARD_WIDTH = 10;
const BOARD_HEIGHT = 20;
const CELL_SIZE = 20;

const TETROMINOS = {
  I: {
    shape: [[1, 1, 1, 1]],
    color: '#00f5ff',
  },
  O: {
    shape: [
      [1, 1],
      [1, 1],
    ],
    color: '#ffff00',
  },
  T: {
    shape: [
      [0, 1, 0],
      [1, 1, 1],
    ],
    color: '#800080',
  },
  S: {
    shape: [
      [0, 1, 1],
      [1, 1, 0],
    ],
    color: '#00ff00',
  },
  Z: {
    shape: [
      [1, 1, 0],
      [0, 1, 1],
    ],
    color: '#ff0000',
  },
  J: {
    shape: [
      [1, 0, 0],
      [1, 1, 1],
    ],
    color: '#0000ff',
  },
  L: {
    shape: [
      [0, 0, 1],
      [1, 1, 1],
    ],
    color: '#ffa500',
  },
};

type TetrominoType = keyof typeof TETROMINOS;

interface Piece {
  shape: number[][];
  color: string;
  x: number;
  y: number;
}

export function TetrisGame({ onBack, onScore }: TetrisGameProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const nextPieceCanvasRef = useRef<HTMLCanvasElement>(null);

  const heldPieceCanvasRef = useRef<HTMLCanvasElement>(null);
  const [heldPiece, setHeldPiece] = useState<Piece | null>(null);
  const hasSwapped = useRef(false); 

  const [board, setBoard] = useState<string[][]>(() =>
    Array(BOARD_HEIGHT).fill(null).map(() => Array(BOARD_WIDTH).fill(''))
  );
  const [currentPiece, setCurrentPiece] = useState<Piece | null>(null);
  const [nextPiece, setNextPiece] = useState<Piece | null>(null);
  const [gameRunning, setGameRunning] = useState(false);
  const [gameOver, setGameOver] = useState(false);
  const [score, setScore] = useState(0);
  const [level, setLevel] = useState(1);
  const [linesCleared, setLinesCleared] = useState(0);

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

  const rotatePiece = (piece: Piece): Piece => {
    const rotated = piece.shape[0].map((_, index) =>
      piece.shape.map(row => row[index]).reverse()
    );
    return { ...piece, shape: rotated };
  };

  const isValidPosition = useCallback((piece: Piece, boardState: string[][]): boolean => {
    for (let y = 0; y < piece.shape.length; y++) {
      for (let x = 0; x < piece.shape[y].length; x++) {
        if (piece.shape[y][x]) {
          const newX = piece.x + x;
          const newY = piece.y + y;
          
          if (
            newX < 0 ||
            newX >= BOARD_WIDTH ||
            newY >= BOARD_HEIGHT ||
            (newY >= 0 && boardState[newY][newX])
          ) {
            return false;
          }
        }
      }
    }
    return true;
  }, []);

  const placePiece = useCallback((piece: Piece, boardState: string[][]): string[][] => {
    const newBoard = boardState.map(row => [...row]);
    
    for (let y = 0; y < piece.shape.length; y++) {
      for (let x = 0; x < piece.shape[y].length; x++) {
        if (piece.shape[y][x]) {
          const boardY = piece.y + y;
          const boardX = piece.x + x;
          if (boardY >= 0) {
            newBoard[boardY][boardX] = piece.color;
          }
        }
      }
    }
    
    return newBoard;
  }, []);

  const clearLines = useCallback((boardState: string[][]): { newBoard: string[][]; clearedLines: number } => {
    const newBoard = [];
    let clearedLines = 0;
    
    for (let y = 0; y < BOARD_HEIGHT; y++) {
      if (boardState[y].every(cell => cell !== '')) {
        clearedLines++;
      } else {
        newBoard.push(boardState[y]);
      }
    }
    
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

  const pauseGame = useCallback(() => {
    setGameRunning(prev => !prev);
  }, []);

  const advanceGame = useCallback((placedPiece: Piece) => {
    hasSwapped.current = false;
    const newBoard = placePiece(placedPiece, board);
    const { newBoard: clearedBoard, clearedLines: lines } = clearLines(newBoard);

    setBoard(clearedBoard);
    const linesPoints = lines > 0 ? (lines * 100 * level) * lines : 0;
    setScore(prev => {
      const newScore = prev + linesPoints + 10;
      if (nextPiece && !isValidPosition(nextPiece, clearedBoard)) {
        setTimeout(() => onScore(newScore), 0);
      }
      return newScore;
    });
    setLinesCleared(prev => {
        const totalLines = prev + lines;
        setLevel(Math.floor(totalLines / 10) + 1);
        return totalLines;
    });

    const newNextPiece = createRandomPiece();
    if (nextPiece && !isValidPosition(nextPiece, clearedBoard)) {
      setGameOver(true);
      setGameRunning(false);
    } else {
      setCurrentPiece(nextPiece);
      setNextPiece(newNextPiece);
    }
  }, [board, clearLines, createRandomPiece, isValidPosition, level, nextPiece, onScore, placePiece]);

  const gameLoop = useCallback(() => {
    if (!gameRunning || gameOver || !currentPiece) return;

    const newPiece = { ...currentPiece, y: currentPiece.y + 1 };

    if (isValidPosition(newPiece, board)) {
      setCurrentPiece(newPiece);
    } else {
      advanceGame(currentPiece);
    }
  }, [gameRunning, gameOver, currentPiece, board, isValidPosition, advanceGame]);

  const hardDrop = useCallback(() => {
    if (!gameRunning || !currentPiece) return;

    const dropPiece = { ...currentPiece };
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
      const newNextPiece = createRandomPiece();
      if (nextPiece && isValidPosition(nextPiece, board)) {
        setCurrentPiece(nextPiece);
        setNextPiece(newNextPiece);
      } else {
        setGameOver(true);
        setGameRunning(false);
      }
    } else if (currentPiece) {
      const newCurrent = { ...heldPiece, x: currentPiece.x, y: currentPiece.y };

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
      if (!gameRunning || !currentPiece) {
          if (e.key === 'Escape') pauseGame();
          return;
      }

      switch (e.key) {
        case 'ArrowLeft': {
          e.preventDefault();
          const newPiece = { ...currentPiece, x: currentPiece.x - 1 };
          if (isValidPosition(newPiece, board)) {
            setCurrentPiece(newPiece);
          }
          break;
        }
        case 'ArrowRight': {
          e.preventDefault();
          const newPiece = { ...currentPiece, x: currentPiece.x + 1 };
          if (isValidPosition(newPiece, board)) {
            setCurrentPiece(newPiece);
          }
          break;
        }
        case 'ArrowDown': {
          e.preventDefault();
          const newPiece = { ...currentPiece, y: currentPiece.y + 1 };
          if (isValidPosition(newPiece, board)) {
            setCurrentPiece(newPiece);
          }
          break;
        }
        case 'ArrowUp': {
          e.preventDefault();
          const rotatedPiece = rotatePiece(currentPiece);
          if (isValidPosition(rotatedPiece, board)) {
            setCurrentPiece(rotatedPiece);
          }
          break;
        }
        case ' ':
        case 'Spacebar': {
          e.preventDefault();
          hardDrop();
          break;
        }

        case 'c':
        case 'C': {
            e.preventDefault();
            swapPiece();
            break;
        }
        case 'Escape':
          e.preventDefault();
          pauseGame();
          break;
      }
    };

    window.addEventListener('keydown', handleKeyPress);
    return () => window.removeEventListener('keydown', handleKeyPress);
  }, [gameRunning, currentPiece, board, isValidPosition, hardDrop, swapPiece, pauseGame]); 

  useEffect(() => {
    if (!gameRunning) return;
    const speed = Math.max(50, 500 - (level - 1) * 50);
    const interval = setInterval(gameLoop, speed);
    return () => clearInterval(interval);
  }, [gameLoop, level, gameRunning]);

  const draw = useCallback(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, BOARD_WIDTH * CELL_SIZE, BOARD_HEIGHT * CELL_SIZE);

    for (let y = 0; y < BOARD_HEIGHT; y++) {
      for (let x = 0; x < BOARD_WIDTH; x++) {
        if (board[y][x]) {
          ctx.fillStyle = board[y][x];
          ctx.fillRect(x * CELL_SIZE, y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
          ctx.strokeStyle = '#ffffff';
          ctx.strokeRect(x * CELL_SIZE, y * CELL_SIZE, CELL_SIZE, CELL_SIZE);
        }
      }
    }

    if (currentPiece) {
      const ghostPiece = { ...currentPiece };
      while (isValidPosition({ ...ghostPiece, y: ghostPiece.y + 1 }, board)) {
        ghostPiece.y++;
      }
      
      ctx.fillStyle = currentPiece.color + '50';
      for (let y = 0; y < ghostPiece.shape.length; y++) {
        for (let x = 0; x < ghostPiece.shape[y].length; x++) {
          if (ghostPiece.shape[y][x]) {
            const drawX = (ghostPiece.x + x) * CELL_SIZE;
            const drawY = (ghostPiece.y + y) * CELL_SIZE;
            ctx.fillRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
          }
        }
      }
    }

    if (currentPiece) {
      ctx.fillStyle = currentPiece.color;
      for (let y = 0; y < currentPiece.shape.length; y++) {
        for (let x = 0; x < currentPiece.shape[y].length; x++) {
          if (currentPiece.shape[y][x]) {
            const drawX = (currentPiece.x + x) * CELL_SIZE;
            const drawY = (currentPiece.y + y) * CELL_SIZE;
            ctx.fillRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
            ctx.strokeStyle = '#ffffff';
            ctx.strokeRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
          }
        }
      }
    }

    ctx.strokeStyle = '#333333';
    ctx.lineWidth = 1;
    for (let x = 0; x <= BOARD_WIDTH; x++) {
      ctx.beginPath();
      ctx.moveTo(x * CELL_SIZE, 0);
      ctx.lineTo(x * CELL_SIZE, BOARD_HEIGHT * CELL_SIZE);
      ctx.stroke();
    }
    for (let y = 0; y <= BOARD_HEIGHT; y++) {
      ctx.beginPath();
      ctx.moveTo(0, y * CELL_SIZE);
      ctx.lineTo(BOARD_WIDTH * CELL_SIZE, y * CELL_SIZE);
      ctx.stroke();
    }
  }, [board, currentPiece, isValidPosition]); 

  const drawSmallPiece = (canvas: HTMLCanvasElement | null, piece: Piece | null) => {
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    ctx.fillStyle = '#000000';
    ctx.fillRect(0, 0, 100, 100);

    if (piece) {
      ctx.fillStyle = piece.color;
      const offsetX = (100 - piece.shape[0].length * CELL_SIZE) / 2;
      const offsetY = (100 - piece.shape.length * CELL_SIZE) / 2;
      
      for (let y = 0; y < piece.shape.length; y++) {
        for (let x = 0; x < piece.shape[y].length; x++) {
          if (piece.shape[y][x]) {
            const drawX = offsetX + x * CELL_SIZE;
            const drawY = offsetY + y * CELL_SIZE;
            ctx.fillRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
            ctx.strokeStyle = '#ffffff';
            ctx.strokeRect(drawX, drawY, CELL_SIZE, CELL_SIZE);
          }
        }
      }
    }
  };

  const drawNextPiece = useCallback(() => {
    drawSmallPiece(nextPieceCanvasRef.current, nextPiece);
  }, [nextPiece]);

  const drawHeldPiece = useCallback(() => {
    drawSmallPiece(heldPieceCanvasRef.current, heldPiece);
  }, [heldPiece]);

  useEffect(() => {
    draw();
    drawNextPiece();
    drawHeldPiece(); 
  }, [draw, drawNextPiece, drawHeldPiece]); 

  return (
    <div className="min-h-screen p-4">
      <div className="text-center py-8">
        <h1 className="text-4xl md:text-6xl tracking-wider mb-4 bg-gradient-to-r from-purple-400 via-violet-400 to-purple-500 bg-clip-text text-transparent">
          TETRIS
        </h1>
        <div className="w-24 h-1 bg-gradient-to-r from-purple-400 to-violet-400 mx-auto"></div>
      </div>

      <div className="flex items-center justify-center">
        <Card className="bg-gray-900/90 border-purple-500 border-2 shadow-2xl shadow-purple-500/50 backdrop-blur-sm">
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
                <div className="grid grid-cols-3 gap-4 text-sm" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                  <div>
                    <div className="text-purple-300">PUNTOS</div>
                    <div className="text-yellow-400">{score}</div>
                  </div>
                  <div>
                    <div className="text-purple-300">NIVEL</div>
                    <div className="text-green-400">{level}</div>
                  </div>
                  <div>
                    <div className="text-purple-300">LÍNEAS</div>
                    <div className="text-cyan-400">{linesCleared}</div>
                  </div>
                </div>
              </div>
              
              <div className="flex gap-2">
                {!gameRunning && !gameOver && (
                  <Button onClick={startGame} size="sm" className="bg-purple-600 hover:bg-purple-700">
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

            <div className="flex justify-center gap-6"> 
              
              <div className="flex flex-col gap-4">
                <div className="bg-black border-2 border-purple-500 p-3 rounded">
                  <div className="text-center text-purple-300 text-xs mb-2" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                    GUARDADA
                  </div>
                  <canvas
                    ref={heldPieceCanvasRef}
                    width={100}
                    height={100}
                    className="bg-black"
                  />
                </div>
              </div>

              <div className="relative">
                <canvas
                  ref={canvasRef}
                  width={BOARD_WIDTH * CELL_SIZE}
                  height={BOARD_HEIGHT * CELL_SIZE}
                  className="border-2 border-purple-500 bg-black"
                />

                {gameOver && (
                  <div className="absolute inset-0 bg-black/80 flex items-center justify-center">
                    <div className="text-center">
                      <h3 className="text-3xl text-red-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>GAME OVER</h3>
                      <p className="text-yellow-400 mb-2" style={{ fontFamily: 'Press Start 2P, monospace' }}>Puntuación Final: {score}</p>
                      <p className="text-cyan-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>Nivel Alcanzado: {level}</p>
                      <Button onClick={startGame} className="bg-purple-600 hover:bg-purple-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                        JUGAR DE NUEVO
                      </Button>
                    </div>
                  </div>
                )}

                {!gameRunning && !gameOver && (linesCleared > 0 || heldPiece) && ( 
                  <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                    <div className="text-center">
                      <h3 className="text-2xl text-yellow-400 mb-4" style={{ fontFamily: 'Press Start 2P, monospace' }}>PAUSADO</h3>
                      <Button onClick={pauseGame} className="bg-purple-600 hover:bg-purple-700" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                        CONTINUAR
                      </Button>
                    </div>
                  </div>
                )}
              </div>

              <div className="flex flex-col gap-4">
                <div className="bg-black border-2 border-purple-500 p-3 rounded">
                  <div className="text-center text-purple-300 text-xs mb-2" style={{ fontFamily: 'Press Start 2P, monospace' }}>
                    SIGUIENTE
                  </div>
                  <canvas
                    ref={nextPieceCanvasRef}
                    width={100}
                    height={100}
                    className="bg-black"
                  />
                </div>
              </div>
            </div>

            <div className="mt-4 text-center text-purple-400 text-xs" style={{ fontFamily: 'Press Start 2P, monospace' }}>
              ←→ Mover • ↓ Bajar • ↑ Rotar • ESPACIO Caída Rápida • C Guardar • ESC Pausar
            </div>
          </div>
        </Card>
      </div>
    </div>
  );
}