import { ref } from 'vue';
import { useToast } from 'vue-toastification';
import type { User } from '../interfaces/user.interface';
import { useAuthStore } from '../stores/auth.store';

export function useRegisterForm() {
  const typeUser = ref(false);
  const cif = ref('');
  const username = ref('');
  const email = ref('');
  const password = ref('');
  const password2 = ref('');

  const authStore = useAuthStore();
  const toast = useToast(); // Instancia de Vue Toastification

  const registerUser = async (): Promise<void> => {
    // Verificar si las contraseñas coinciden
    if (password.value !== password2.value) {
      toast.error('Las contraseñas no coinciden.');
      return;
    }

    const userData: User = {
      name_user: username.value,
      email_user: email.value,
      password_user: password.value,
      type_user: typeUser.value ? 'COMPANY' : 'CLIENT',
      cif_user: typeUser.value ? cif.value : null,
    };

    const { ok, message } = await authStore.register(userData);

    if (ok) {
      toast.success(message); // Mostrar toast de éxito
    } else {
      toast.error('Error al registrar usuario: ' + message); // Mostrar toast de error
    }
  };

  return {
    typeUser,
    cif,
    username,
    email,
    password,
    password2,
    registerUser,
  };
}
