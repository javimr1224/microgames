import React, { useState, useEffect } from 'react';
import { Routes, Route, useNavigate } from 'react-router-dom';
import { GameMenu } from './components/GameMenu';
import { SnakeGame } from './components/SnakeGame';
import { PongGame } from './components/PongGame';
import { TetrisGame } from './components/TetrisGame';
import { BreakoutGame } from './components/BreakoutGame';
import { saveScore, setupAxios } from './services/scoreService';

export type GameType = 'menu' | 'snake' | 'pong' | 'tetris' | 'breakout';

export default function App() {
  const [scores, setScores] = useState<Record<string, number>>({
    snake: 0,
    pong: 0,
    tetris: 0,
    breakout: 0,
  });
  const [apiMessage, setApiMessage] = useState<string>('Connecting...');
  const navigate = useNavigate();

  useEffect(() => {
    setupAxios();

    const apiUrl = import.meta.env.VITE_API_URL;
    if (apiUrl) {
      fetch(`${apiUrl}/test`)
        .then(res => {
          if (!res.ok) {
            throw new Error(`HTTP error, status: ${res.status}`);
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

  const handleScore = async (game: string, score: number) => {
    if (score > (scores[game] || 0)) {
      setScores(prev => ({
        ...prev,
        [game]: score
      }));
      try {
        await saveScore(game, score);
        console.log(`Score for ${game} saved successfully.`);
      } catch (error) {
        console.error(`Failed to save score for ${game}.`);
      }
    }
  };

  const handleSelectGame = (game: GameType) => {
    navigate(`/${game}`);
  };

  const handleBackToMenu = () => {
    navigate('/');
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
        <Routes>
          <Route path="/" element={<GameMenu onSelectGame={handleSelectGame} scores={scores} />} />
          <Route path="/snake" element={<SnakeGame onBack={handleBackToMenu} onScore={(score) => handleScore('snake', score)} />} />
          <Route path="/pong" element={<PongGame onBack={handleBackToMenu} onScore={(score) => handleScore('pong', score)} />} />
          <Route path="/tetris" element={<TetrisGame onBack={handleBackToMenu} onScore={(score) => handleScore('tetris', score)} />} />
          <Route path="/breakout" element={<BreakoutGame onBack={handleBackToMenu} onScore={(score) => handleScore('breakout', score)} />} />
        </Routes>
      </div>
    </div>
  );
}