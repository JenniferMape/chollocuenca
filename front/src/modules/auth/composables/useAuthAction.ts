import { ref } from 'vue';
import { useLocalStorage } from '@vueuse/core';
import { AuthStatus, type User } from '../interfaces';
import { defineStore } from 'pinia';
import { tesloApi } from '@/api/tesloApi';

export const useAuthStore = defineStore('auth', () => {
  const authStatus = ref<AuthStatus>(AuthStatus.Checking);
  const user = ref<User | undefined>();

  const token = useLocalStorage<string | null>('authToken', null);

  const login = async (email: string, password: string): Promise<boolean> => {
    try {
      const { data } = await tesloApi.post('/register/login', {
        email_user: email,
        password_user: password,
      });

      if (data?.result?.token) {
        token.value = data.result.token;
        user.value = data.result.user;
        authStatus.value = AuthStatus.Authenticated;

        return true; // Login exitoso
      }

      logout(); // Si no hay token, cerrar sesión
      return false;
    } catch (error) {
      logout();
      return false;
    }
  };

  const logout = () => {
    token.value = null; // Limpiar el token
    user.value = undefined;
    authStatus.value = AuthStatus.Unauthenticated;
    localStorage.removeItem('authToken');
  };

  return {
    user,
    token, // token es un `RemovableRef<string | null>`
    authStatus,
    login,
    logout,
  };
});
