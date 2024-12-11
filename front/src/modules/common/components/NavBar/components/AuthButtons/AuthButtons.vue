<template>
  <div class="flex items-center">
    <template v-if="!authStore.isAuthenticated">
      <router-link to="/auth/login" class="btn btn-primary ml-4"
        >Regístrate / Inicia sesión</router-link
      >
    </template>
    <template v-else>
      <span class="mr-2">Hola, {{ authStore.user?.name_user || 'Usuario' }}</span>
      <UserMenu />
    </template>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/modules/auth/stores/auth.store';
import UserMenu from './components/UserMenu.vue';

const authStore = useAuthStore();
</script>
