import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { AuthStatus, type AuthResponse, type User } from '@/modules/auth/interfaces';
import { tesloApi } from '@/api/tesloApi';
import { useLocalStorage } from '@vueuse/core';
import { useRouter } from 'vue-router';

export const useAuthStore = defineStore('auth', () => {
  const authStatus = ref<AuthStatus>(AuthStatus.Checking);
  const user = ref<User | undefined>();
  const token = useLocalStorage<string | null>('authToken', null);
  const router = useRouter();
  // Acción de inicio de sesión
  const login = async (email: string, password: string): Promise<boolean> => {
    try {
      const { data } = await tesloApi.post<AuthResponse>('/register/login', {
        email_user: email,
        password_user: password,
      });

      if (data?.result?.token) {
        token.value = data.result.token;
        user.value = data.result.user;
        authStatus.value = AuthStatus.Authenticated;

        localStorage.setItem('authToken', data.result.token);

        return true;
      } else {
        return false;
      }
    } catch (error) {
      return false;
    }
  };
  // Acción para registrar un nuevo usuario
  const register = async (userData: User) => {
    try {
      const response = await tesloApi.post<AuthResponse>('/register/new', userData, {
        headers: {
          'Content-Type': 'application/json',
        },
      });
      const data = response.data;

      if (data?.result?.token) {
        token.value = data.result.token;
        user.value = data.result.user;
        authStatus.value = AuthStatus.Authenticated;

        localStorage.setItem('authToken', data.result.token);

        return { ok: true, message: data.message };
      } else {
        return {
          ok: false,
          message: data?.message || 'Error al registrar el usuario',
        };
      }
    } catch (error: any) {
      return {
        ok: false,
        message: error.response?.data?.message || 'Error al registrar el usuario',
      };
    }
  };

  // Acción para cerrar sesión
  const logout = () => {
    router.push({ name: 'home' });

    authStatus.value = AuthStatus.Unauthenticated;
    localStorage.removeItem('authToken');
    token.value = null;
    user.value = undefined;
  };

  // Comprobar el estado de autenticación

  const checkAuthStatus = async () => {
    const storedToken = localStorage.getItem('authToken');
    try {
      const { data } = await tesloApi.get<AuthResponse>('/auth/checkToken', {
        headers: {
          Authorization: `Bearer ${storedToken}`,
        },
      });
      if (data?.result?.user) {
        user.value = data.result.user;
        token.value = data.result.token;
        authStatus.value = AuthStatus.Authenticated;
        return { ok: true, message: data?.message || 'Usuario autenticado correctamente.' };
      } else {
        return { ok: false, message: data?.message || 'Usuario no encontrado.' };
      }
    } catch (error) {
      console.error('Error en la verificación del token:', error);
      return { ok: false, message: 'Error al verificar la autenticación.' };
    }
  };
  const updateUser = (updatedFields: Partial<User>) => {
    if (user.value) {
      user.value = { ...user.value, ...updatedFields };
    }
  };

  return {
    user,
    token,
    authStatus,

    // Getters
    isChecking: computed(() => authStatus.value === AuthStatus.Checking),
    isAuthenticated: computed(() => authStatus.value === AuthStatus.Authenticated),
    username: computed(() => user.value ?? ''),
    isAdmin: computed(() => user.value?.type_user.includes('admin') ?? false),
    isCompany: computed(() => user.value?.type_user.includes('COMPANY') ?? false),
    isClient: computed(() => user.value?.type_user.includes('CLIENT') ?? false),

    // Actions
    login,
    logout,
    checkAuthStatus,
    register,
    updateUser,
  };
});
