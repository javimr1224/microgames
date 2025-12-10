import { useLocation } from 'react-router-dom';
import React from 'react';

interface GameRouteProps {
  component: React.ComponentType<any>;
  [key: string]: any;
}

export function GameRoute({ component: Component, ...rest }: GameRouteProps) {
  const location = useLocation();
  const params = new URLSearchParams(location.search);
  const fromMenu = params.get('from') === 'menu';

  return <Component {...rest} fromMenu={fromMenu} />;
}
