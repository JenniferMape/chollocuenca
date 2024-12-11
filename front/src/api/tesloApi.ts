import axios from 'axios';

const tesloApi = axios.create({
  baseURL: import.meta.env.VITE_TESLO_API_URL,
});

tesloApi.interceptors.request.use((config) => {
  const storedToken = localStorage.getItem('authToken');
  if (storedToken) {
    config.headers.Authorization = `Bearer ${storedToken}`;
  }
  return config;
});

export { tesloApi };
