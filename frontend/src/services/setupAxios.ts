import axios from 'axios';

export const setupAxios = () => {
  axios.defaults.withCredentials = true;

  console.log('API URL detectada:', import.meta.env.VITE_API_URL);

  axios.defaults.baseURL = import.meta.env.VITE_API_URL || '';
};

export const getCsrfCookie = async () => {
  try {
    await axios.get('/sanctum/csrf-cookie');
  } catch (error) {
    console.error('Error fetching CSRF cookie:', error);
    throw error;
  }
};

export const getUser = async () => {
  try {
    const response = await axios.get('/api/user');
    return response.data;
  } catch (error) {
    console.error('Error fetching user:', error);
    throw error;
  }
};

export const login = async (email: string, password: string) => {
  try {
    await getCsrfCookie();
    await axios.post('/login', { email, password });
    const user = await getUser();
    return user;
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
