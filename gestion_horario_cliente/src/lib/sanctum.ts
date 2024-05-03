import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8080',
  withCredentials: true,
});

export const login = async (user_name: string, password: string) => {
  const response = await api.post('/login', { user_name, password });
  return response.data;
};
/*
export const logout = async () => {
  const response = await api.post('/logout');
  return response.data;
};

export const getUser = async () => {
  const response = await api.get('/api/user');
  return response.data;
};
*/