<template>
  <!-- Formulario de Recuperación de Contraseña -->
  <div class="card bg-base-100 w-full shadow-2xl">
    <form @submit.prevent="onSubmit" class="card-body p-6 grid grid-cols-1 gap-4">
      <h2 class="card-title text-lg mb-2 col-span-full text-neutral">Recuperar tu contraseña</h2>

      <!-- Campo de Email -->
      <div class="form-control col-span-full">
        <label class="text-neutral">
          Email
          <FormKit
            type="email"
            name="email"
            prefix-icon="email"
            v-model="emailuser"
            placeholder="ejemplo@ejemplo.com"
            validation-visibility="blur"
            validation="required|email"
          />
        </label>
      </div>

      <!-- Botón para Enviar Correo -->
      <div class="form-control mt-6">
        <button class="btn btn-primary" type="submit" :disabled="loading">
          {{ loading ? 'Enviando...' : 'Enviar enlace de recuperación' }}
        </button>
      </div>

      <!-- Enlace para Volver al Inicio de Sesión -->
      <div class="mt-6 text-center">
        <RouterLink class="hover:underline" :to="{ name: 'login' }">
          Volver al inicio de sesión
        </RouterLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { usePasswordReset } from '@/modules/auth/composables/usePasswordReset';
import { RouterLink } from 'vue-router';
import { FormKit } from '@formkit/vue';

const { emailuser, onSubmit, loading } = usePasswordReset();
</script>
