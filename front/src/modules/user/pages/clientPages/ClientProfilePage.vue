<template>
  <section class="w-full bg-base-100 shadow-md p-6 rounded-lg ml-6 min-h-screen">
    <h2 class="font-bold text-2xl mb-8">Perfil</h2>
    <h3 class="font-medium"><i class="fas fa-user mr-2"></i> Tu avatar</h3>
    <!-- Avatar -->
    <div class="flex flex-col items-center mb-8">
      <div class="avatar mb-4">
        <div class="w-[250px] h-[250px] rounded-full overflow-hidden">
          <img :src="authStore.user.avatar_user || defaultAvatar" alt="Tu avatar" />
        </div>
      </div>
      <div class="flex space-x-4">
        <button @click="$refs.avatarInput.click()" class="btn btn-outline btn-secondary">
          Reemplazar
        </button>
        <button @click="removeAvatar(id)" class="btn btn-outline btn-error">Quitar</button>
      </div>
      <p class="text-sm text-center mt-2">Para resultados óptimos utiliza una imagen cuadrada</p>
      <input type="file" @change="replaceAvatar" class="hidden" ref="avatarInput" />
      <button
        @click="updateAvatar(avatar, id)"
        class="btn btn-sm btn-secondary mt-4"
        :disabled="!avatar"
      >
        Subir Avatar
      </button>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <!-- Nombre de usuario -->
      <div>
        <h3 class="font-medium"><i class="fas fa-signature mr-2"></i> Tu nombre de usuario</h3>
      </div>
      <div>
        <p class="text-lg font-bold text-neutral">{{ authStore.user.name_user }}</p>
        <button @click="openModal('username')" class="btn btn-outline btn-secondary w-full mt-2">
          Cambiar nombre de usuario
        </button>
      </div>

      <!-- Email -->
      <div>
        <h3 class="font-medium"><i class="fas fa-envelope mr-2"></i>Tu email</h3>
      </div>
      <div>
        <p class="text-lg font-bold text-neutral">{{ authStore.user.email_user }}</p>
        <button @click="openModal('email')" class="btn btn-outline btn-secondary mt-2 w-full">
          Cambiar email
        </button>
      </div>

      <!-- Contraseña -->
      <div>
        <h3 class="font-medium"><i class="fas fa-key mr-2"></i>Establecer contraseña</h3>
      </div>
      <div>
        <p class="text-lg font-bold text-neutral">********</p>
        <button @click="openModal('password')" class="btn btn-outline btn-secondary mt-2 w-full">
          Cambiar contraseña
        </button>
      </div>

      <!-- Eliminar Cuenta -->
      <div>
        <h3 class="font-medium"><i class="fas fa-trash-can mr-2"></i> Eliminar Cuenta</h3>
      </div>
      <div>
        <button @click="showDeleteModal = true" class="btn btn-outline btn-error px-6 w-full">
          Eliminar Cuenta
        </button>
      </div>
    </div>

    <!-- Modal Editar -->
    <div v-if="showModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg">Editar {{ fieldLabels[modalField] }}</h3>
        <input
          v-model="modalValue"
          :type="modalField === 'password' ? 'password' : 'text'"
          class="input input-bordered w-full mt-4"
          :class="{ 'input-error': modalError }"
          :placeholder="'Nuevo ' + fieldLabels[modalField]"
        />
        <div v-if="modalError" class="text-red-500 text-sm mt-2">
          {{ modalErrorMessage }}
        </div>
        <div class="modal-action">
          <button
            @click="saveChanges"
            class="btn btn-info"
            :disabled="!modalValue || modalValue.length === 0"
          >
            Guardar
          </button>
          <button @click="closeModal" class="btn btn-secondary">Cancelar</button>
        </div>
      </div>
    </div>

    <!-- Modal Confirmación -->
    <div v-if="showDeleteModal" class="modal modal-open">
      <div class="modal-box">
        <h3 class="font-bold text-lg text-red-500">Confirmar eliminación de cuenta</h3>
        <p class="py-4">
          ¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer y
          perderás todos tus datos.
        </p>
        <div class="modal-action">
          <button @click="confirmDeleteAccount" class="btn btn-error">Confirmar</button>
          <button @click="closeDeleteModal" class="btn btn-secondary">Cancelar</button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/modules/auth/stores/auth.store';
import defaultAvatar from '@/assets/usuario.png';
import { useToast } from 'vue-toastification';
import {
  changeUsername,
  changeEmail,
  changePassword,
  removeAvatar,
  updateAvatar,
  deleteAccount,
} from '@/modules/user/composables/useProfile';

const authStore = useAuthStore();
const toast = useToast();

// Modal state
const showModal = ref(false);
const modalField = ref('');
const modalValue = ref('');
const modalError = ref(false);
const modalErrorMessage = ref('');
const showDeleteModal = ref(false);

const fieldLabels = {
  username: 'nombre de usuario',
  email: 'correo electrónico',
  password: 'contraseña',
};
// Avatar state
const avatar = ref(null);
const id = authStore.user?.id;

// Functions
const openModal = (field) => {
  modalField.value = field;
  modalValue.value = field === 'password' ? '' : authStore.user[field + '_user'];
  modalError.value = false;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  modalField.value = '';
  modalValue.value = '';
};

const saveChanges = async () => {
  // Validación del campo según el tipo
  if (modalField.value === 'username') {
    if (!modalValue.value.trim()) {
      modalError.value = true;
      modalErrorMessage.value = 'El nombre de usuario no puede estar vacío.';
      return;
    }
    if (modalValue.value.length < 3) {
      modalError.value = true;
      modalErrorMessage.value = 'El nombre de usuario debe tener al menos 3 caracteres.';
      return;
    }
  }

  if (modalField.value === 'email') {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(modalValue.value)) {
      modalError.value = true;
      modalErrorMessage.value = 'Por favor ingresa un correo electrónico válido.';
      return;
    }
  }

  if (modalField.value === 'password') {
    if (modalValue.value.length < 6) {
      modalError.value = true;
      modalErrorMessage.value = 'La contraseña debe tener al menos 6 caracteres.';
      return;
    }
  }

  // Si pasa la validación, continuar con la actualización

  if (modalField.value === 'username') await changeUsername(modalValue.value, id);
  if (modalField.value === 'email') await changeEmail(modalValue.value, id);
  if (modalField.value === 'password') await changePassword(modalValue.value, id);

  closeModal();
};
const closeDeleteModal = () => {
  showDeleteModal.value = false;
};
const confirmDeleteAccount = () => {
  deleteAccount(id);

  showDeleteModal.value = false;
};
const replaceAvatar = (event) => {
  const file = event.target.files[0];
  if (file) avatar.value = file;
};
</script>
