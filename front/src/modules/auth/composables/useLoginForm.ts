import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './useAuthAction';
import { useToast } from 'vue-toastification';

export function useLoginForm() {
  const emailuser = ref('');
  const passworduser = ref('');
  const rememberMe = ref(false);
  const errorMessage = ref<string | null>(null);
  const loading = ref(false);
  const router = useRouter();
  const toast = useToast();
  const authStore = useAuthStore();

  const onLogin = async (): Promise<void> => {
    if (!emailuser.value || !passworduser.value) {
      errorMessage.value = 'Por favor ingrese el nombre de usuario y la contraseña.';
      toast.error(errorMessage.value);
      return;
    }

    const email = emailuser.value;
    const password = passworduser.value;
    loading.value = true;
    errorMessage.value = null;

    try {
      const ok = await authStore.login(email, password);

      if (!ok) {
        toast.error('Correo electrónico o contraseña incorrectos.');
        return;
      }

      // Redirigir solo si el inicio de sesión fue exitoso
      router.push({ name: 'home' });
    } catch (error: any) {
      if (error.response) {
        errorMessage.value = error.response.data.message || 'Error en el inicio de sesión.';
        toast.error(errorMessage.value);
      } else {
        errorMessage.value = 'Error al conectarse con el servidor.';
        toast.error(errorMessage.value);
      }
    } finally {
      loading.value = false;
    }
  };

  return {
    emailuser,
    passworduser,
    rememberMe,
    errorMessage,
    loading,
    onLogin,
  };
}
