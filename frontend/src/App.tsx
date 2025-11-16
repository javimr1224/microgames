import React, { useState, useEffect } from 'react';
import { GameMenu } from './components/GameMenu';
import { SnakeGame } from './components/SnakeGame';
import { PongGame } from './components/PongGame';
import { TetrisGame } from './components/TetrisGame';
import { BreakoutGame } from './components/BreakoutGame';

export type GameType = 'menu' | 'snake' | 'pong' | 'tetris' | 'breakout';

export default function App() {
  const [currentGame, setCurrentGame] = useState<GameType>('menu');
  const [scores, setScores] = useState<Record<string, number>>({
    snake: 0,
    pong: 0,
    tetris: 0,
    breakout: 0,
  });
  const [apiMessage, setApiMessage] = useState<string>('Connecting to backend...');

  useEffect(() => {
    const apiUrl = import.meta.env.VITE_API_URL;
    if (apiUrl) {
      fetch(`${apiUrl}/test`)
        .then(res => {
          if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
          }
          return res.json();
        })
        .then(data => setApiMessage(data.message || 'Connected, but no message received.'))
        .catch(error => {
          console.error("Could not fetch from API:", error);
          setApiMessage('Failed to connect to backend. Is the Laravel server running?');
        });
    } else {
      setApiMessage('Backend API URL not configured.');
    }
  }, []);

  const updateScore = (game: string, score: number) => {
    setScores(prev => ({
      ...prev,
      [game]: Math.max(prev[game] || 0, score)
    }));
  };

  const renderGame = () => {
    switch (currentGame) {
      case 'snake':
        return <SnakeGame onBack={() => setCurrentGame('menu')} onScore={(score) => updateScore('snake', score)} />;
      case 'pong':
        return <PongGame onBack={() => setCurrentGame('menu')} onScore={(score) => updateScore('pong', score)} />;
      case 'tetris':
        return <TetrisGame onBack={() => setCurrentGame('menu')} onScore={(score) => updateScore('tetris', score)} />;
      case 'breakout':
        return <BreakoutGame onBack={() => setCurrentGame('menu')} onScore={(score) => updateScore('breakout', score)} />;
      default:
        return <GameMenu onSelectGame={setCurrentGame} scores={scores} />;
    }
  };

  return (
    <div className="min-h-screen bg-black relative overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br bg-cyan-500 via-emerald-900 to-black">
        <div className="absolute inset-0 opacity-20" style={{
          backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cdefs%3E%3Cpattern id='grid' width='10' height='10' patternUnits='userSpaceOnUse'%3E%3Cpath d='M 10 0 L 0 0 0 10' fill='none' stroke='%23ffffff08' stroke-width='1'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23grid)'/%3E%3C/svg%3E")`
        }}></div>
        <div className="absolute top-10 left-10 w-32 h-32 bg-cyan-500 rounded-full blur-3xl opacity-20 animate-pulse"></div>
        <div className="absolute top-1/2 right-20 w-40 h-40 bg-red-500 rounded-full blur-3xl opacity-20 animate-pulse animation-delay-1000"></div>
        <div className="absolute bottom-20 left-1/3 w-36 h-36 bg-yellow-500 rounded-full blur-3xl opacity-20 animate-pulse animation-delay-2000"></div>
      </div>
      
      <div className="relative z-10">
        <div className="absolute top-2 left-1/2 -translate-x-1/2 text-white text-xs bg-black/30 px-3 py-1 rounded-full">
          {apiMessage}
        </div>
        {renderGame()}
      </div>
    </div>
  );
}