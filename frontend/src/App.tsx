import React, { useState, useEffect } from 'react';
import { Routes, Route, useNavigate } from 'react-router-dom';
import { GameRoute } from './components/GameRoute';
import { GameMenu } from './components/GameMenu';
import { SnakeGame } from './components/SnakeGame';
import { PongGame } from './components/PongGame';
import { TetrisGame } from './components/TetrisGame';
import { BreakoutGame } from './components/BreakoutGame';
import axios from 'axios';

export type GameType = 'menu' | 'snake' | 'pong' | 'tetris' | 'breakout';

export interface User {
  id: number;
  name: string;
  email: string;
}

axios.defaults.baseURL = import.meta.env.VITE_API_URL || '';
axios.defaults.withCredentials = true;

const getCsrfCookie = async () => {
  await axios.get('/sanctum/csrf-cookie');
};

const getUser = async () => {
  const res = await axios.get('/api/user');
  return res.data;
};

const login = async (email: string, password: string) => {
  await getCsrfCookie();
  const res = await axios.post('/login', { email, password }); 
  return res.data.user; 
};

export default function App() {
  const [user, setUser] = useState<User | null>(null);
  const [scores, setScores] = useState<Record<string, number>>({
    snake: 0,
    pong: 0,
    tetris: 0,
    breakout: 0,
  });
  const [apiMessage, setApiMessage] = useState<string>('Connecting...');
  const navigate = useNavigate();

  useEffect(() => {
    const checkUser = async () => {
      try {
        await getCsrfCookie();
        const userData = await getUser();
        setUser(userData);
      } catch {
        setUser(null);
      }
    };
    checkUser();

    const apiUrl = import.meta.env.VITE_API_URL || '';
      fetch(`${apiUrl}/api/test`)
        .then(res => res.json())
        .then(data => setApiMessage(data.message || 'Connected'))
        .catch(() => setApiMessage('Failed to connect to backend.'));
  }, []);

  const handleLogin = async (email: string, password: string) => {
    try {
      const userData = await login(email, password);
      setUser(userData);
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const handleScore = async (game: string, score: number) => {
    if (user && score > (scores[game] || 0)) {
      setScores(prev => ({ ...prev, [game]: score }));
      try {
        await axios.post('/api/scores', { game_id: game, score });
        console.log(`Score for ${game} saved successfully.`);
      } catch (error) {
        console.error(`Failed to save score for ${game}:`, error);
      }
    }
  };

  const handleSelectGame = (game: GameType) => navigate(`/${game}?from=menu`);
  const handleBackToMenu = () => navigate('/');

  return (
    <div className="min-h-screen bg-black relative overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br bg-cyan-500 via-emerald-900 to-black"></div>
      <div className="relative z-10">
        <div className="absolute top-2 left-1/2 -translate-x-1/2 text-white text-xs bg-black/30 px-3 py-1 rounded-full">
          {apiMessage}
        </div>
        <Routes>
          <Route path="/" element={
            <GameMenu
              onSelectGame={handleSelectGame}
              scores={scores}
              onLogin={handleLogin}
              user={user}
            />
          }/>
          <Route path="/snake" element={<GameRoute component={SnakeGame} onBack={handleBackToMenu} onScore={(score: number) => handleScore('snake', score)} />} />
          <Route path="/pong" element={<GameRoute component={PongGame} onBack={handleBackToMenu} onScore={(score: number) => handleScore('pong', score)} />} />
          <Route path="/tetris" element={<GameRoute component={TetrisGame} onBack={handleBackToMenu} onScore={(score: number) => handleScore('tetris', score)} />} />
          <Route path="/breakout" element={<GameRoute component={BreakoutGame} onBack={handleBackToMenu} onScore={(score: number) => handleScore('breakout', score)} />} />
        </Routes>
      </div>
    </div>
  );
}
