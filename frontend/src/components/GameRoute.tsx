import { useLocation, useNavigate } from 'react-router-dom';
import React, { useState, useEffect } from 'react';
import { User } from '../App';
import axios from 'axios';

interface RoutedGameProps {
  onBack: () => void;
  onScore: (score: number) => void;
  fromMenu?: boolean;
  user?: User | null;
}

interface GameRouteProps {
  component: React.ComponentType<RoutedGameProps>;
  user: User | null;
  onBack: () => void;
  onScore: (score: number) => void;
}

export function GameRoute({ component: Component, user, onBack, onScore }: GameRouteProps) {
  const location = useLocation();
  const navigate = useNavigate();
  const params = new URLSearchParams(location.search);
  const fromMenu = params.get('from') === 'menu';

  const gameName = location.pathname.replace('/', '');

  const requiresPurchase = gameName === 'skybound';
  const [purchasedAccess, setPurchasedAccess] = useState<boolean | null>(null);

  useEffect(() => {
    if (!requiresPurchase || !user) return;

    let cancelled = false;
    axios.get('/api/my-games')
        .then(response => {
          const purchasedGames = response.data;
          if (!cancelled) {
            setPurchasedAccess(Array.isArray(purchasedGames) && purchasedGames.some(game => game.slug === 'skybound'));
          }
        })
        .catch(() => {
          if (!cancelled) setPurchasedAccess(false);
        });

    return () => {
      cancelled = true;
    };
  }, [requiresPurchase, user]);

  const hasAccess = requiresPurchase ? (user ? purchasedAccess : false) : true;

  if (hasAccess === null) {
    return <div className="z-20 flex flex-col items-center justify-center h-screen text-white">Loading...</div>;
  }

  if (hasAccess === false) {
    return (
      <div className="z-20 flex flex-col items-center justify-center h-screen text-white">
        <div className="text-center p-8 bg-black/50 rounded-lg ">
          <h1 className="text-4xl font-bold mb-4 text-red-500">Acceso Denegado</h1>
          <p className="text-xl mb-6">Necesitas comprar este juego para poder jugarlo.</p>
          <button
            onClick={() => navigate('/')}
            className="p-3 mb-4 py-3 bg-cyan-500 text-white font-bold rounded-lg shadow-lg hover:bg-cyan-600 transition-colors"
          >
            Volver al Menú
          </button>
        </div>
      </div>
    );
  }

  return <Component onBack={onBack} onScore={onScore} fromMenu={fromMenu} user={user} />;
}
