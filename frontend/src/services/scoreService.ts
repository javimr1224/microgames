import axios from 'axios';

const API_URL = '/api';

export const setupAxios = () => {
  axios.defaults.withCredentials = true;
  axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000'; // Ensure base URL is set
};

export const getCsrfCookie = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');
  } catch (error) {
    console.error('Error fetching CSRF cookie:', error);
    throw error;
  }
};

export const login = async (email: string, password: string) => {
  try {
    await getCsrfCookie(); // Get CSRF cookie before logging in
    const response = await axios.post('/login', { email, password });
    return response.data;
  } catch (error) {
    console.error('Error logging in:', error);
    throw error;
  }
};

export const logout = async () => {
  try {
    const response = await axios.post('/logout');
    return response.data;
  } catch (error) {
    console.error('Error logging out:', error);
    throw error;
  }
};

export const saveScore = async (gameId: string, score: number) => {
  try {
    const response = await axios.post(`${API_URL}/scores`, {
      game_id: gameId,
      score: score,
    });
    return response.data;
  } catch (error) {
    console.error('Error saving score:', error);
    throw error;
  }
};
