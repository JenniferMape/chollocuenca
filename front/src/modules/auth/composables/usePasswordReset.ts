import { ref } from 'vue';
import { tesloApi } from '@/api/tesloApi';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';

export function usePasswordReset() {
  const emailuser = ref('');
  const loading = ref(false);
  const router = useRouter();
  const toast = useToast();

  const onSubmit = async () => {
    loading.value = true;
    try {
      // Hacer la solicitud a la API para enviar el enlace de recuperación
      await tesloApi.post('/register/recovery', { email_user: emailuser.value });
      toast.success('Se ha enviado un enlace de recuperación a tu correo electrónico.');
      router.push({ name: 'login' });
    } catch (error) {
      toast.error('Hubo un error al intentar enviar el enlace de recuperación.');
    } finally {
      loading.value = false;
    }
  };

  return { emailuser, onSubmit, loading };
}
