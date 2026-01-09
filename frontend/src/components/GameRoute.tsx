import { useLocation, useNavigate } from 'react-router-dom';
import React, { useState, useEffect } from 'react';
import { User } from '../App';
import axios from 'axios';

interface GameRouteProps {
  component: React.ComponentType<any>;
  user: User | null;
  [key: string]: any;
}

export function GameRoute({ component: Component, user, ...rest }: GameRouteProps) {
  const location = useLocation();
  const navigate = useNavigate();
  const params = new URLSearchParams(location.search);
  const fromMenu = params.get('from') === 'menu';

  const gameName = location.pathname.replace('/', '');

  const [hasAccess, setHasAccess] = useState<boolean | null>(null);

  useEffect(() => {
    if (gameName === 'skybound') {
      if (!user) {
        setHasAccess(false);
        return;
      }

      axios.get('/api/my-games')
        .then(response => {
          const purchasedGames = response.data;
          if (Array.isArray(purchasedGames) && purchasedGames.some(game => game.slug === 'skybound')) {
            setHasAccess(true);
          } else {
            setHasAccess(false);
          }
        })
        .catch(() => {
          setHasAccess(false);
        });
    } else {
      setHasAccess(true);
    }
  }, [gameName, user]);

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

  return <Component {...rest} fromMenu={fromMenu} user={user} />;
}
