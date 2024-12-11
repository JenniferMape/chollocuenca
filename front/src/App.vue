<template>
  <RouterView />
</template>

<script setup lang="ts">
import { useAuthStore } from '@/modules/auth/stores/auth.store';
import { RouterView } from 'vue-router';
import { useRoute, useRouter } from 'vue-router';
import { AuthStatus } from '@/modules/auth/interfaces/auth-status.enum';
import '@fortawesome/fontawesome-free/css/all.css';
import '@fortawesome/fontawesome-free/js/all.js';
const authStore = useAuthStore();

const router = useRouter();
const route = useRoute();

authStore.$subscribe(
  (_: any, state: { authStatus: AuthStatus }) => {
    if (state.authStatus === AuthStatus.Checking) {
      authStore.checkAuthStatus();
      return;
    }

    if (route.path.includes('/auth') && state.authStatus === AuthStatus.Authenticated) {
      router.replace({ name: 'home' });
      return;
    }
  },
  {
    immediate: true,
  },
);
</script>

<style scoped></style>
