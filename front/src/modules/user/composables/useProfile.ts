// useProfileFunctions.ts
import { useToast } from 'vue-toastification';
import { tesloApi } from '@/api/tesloApi';
import { useAuthStore } from '@/modules/auth/stores/auth.store';
import axios from 'axios';

const toast = useToast();
const authStore = useAuthStore();

export const changeUsername = async (username: string, id: number) => {
  try {
    const { data } = await tesloApi.put('/account', {
      id: id,
      name_user: username,
    });
    if (data.status === 200) {
      toast.success(data.message);
      authStore.updateUser({ name_user: username });
    }
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};

export const changeEmail = async (email: string, id: number) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    toast.error('Por favor, ingresa un email válido.');
    return;
  }
  try {
    const { data } = await tesloApi.put('/account', {
      id: id,
      email_user: email,
    });
    if (data.status === 200) {
      toast.success(data.message);
      authStore.updateUser({ email_user: email });
    }
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};

export const changePassword = async (password: string, id: number) => {
  if (password.length < 8) {
    toast.error('La contraseña debe tener al menos 8 caracteres.');
    return;
  }
  try {
    const { data } = await tesloApi.put('/account', {
      id: id,
      password_user: password,
    });
    if (data.status === 200) {
      toast.success(data.message);
    }
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};

export const changeCIF = async (cif: string, id: number) => {
  const cifRegex = /^[A-Z]\d{8}$/;
  if (!cifRegex.test(cif)) {
    toast.error('Por favor, ingresa un CIF válido.');
    return;
  }
  try {
    const { data } = await tesloApi.put('/account', {
      id: id,
      cif_user: cif,
    });
    if (data.status === 200) {
      toast.success(data.message);
    }
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};

export const removeAvatar = async (id: number) => {
  try {
    const response = await tesloApi.delete(`/account/avatar?id=${id}`);
    toast.success(response.data.message);
    authStore.updateUser({ avatar_user: null });
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};

export const updateAvatar = async (file: File, id: number) => {
  if (!file) {
    toast.error('Por favor, selecciona un avatar antes de subir.');
    return;
  }

  try {
    const formData = new FormData();
    formData.append('avatar', file);

    const response = await tesloApi.post(`/account/avatar?id=${id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    toast.success(response.data.message);
    authStore.updateUser({ avatar_user: response.data.result });
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};

export const deleteAccount = async (id: number) => {
  try {
    const response = await tesloApi.delete(`/account/${id}`);
    toast.success(response.data.message);
    authStore.logout();
  } catch (error) {
    if (axios.isAxiosError(error) && error.response) {
      toast.error(error.response.data.error);
    } else {
      toast.error('Ha ocurrido un error inesperado');
    }
  }
};
