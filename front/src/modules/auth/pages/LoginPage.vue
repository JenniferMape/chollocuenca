<template>
  <!-- Formulario de Inicio de Sesión -->
  <div class="card bg-base-100 w-full shadow-2xl">
    <form @submit.prevent="onLogin" class="card-body p-6 grid grid-cols-1 gap-4">
      <h2 class="card-title text-lg mb-2 col-span-full text-neutral">Si ya eres usuario:</h2>

      <!-- Campo de Email -->
      <div class="form-control col-span-full">
        <label class=" text-neutral">
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

      <!-- Campo de Contraseña -->
      <div class="form-control col-span-full">
        <label class="text-neutral">
          Password
          <FormKit
            type="password"
            name="contraseña"
            prefix-icon="password"
            v-model="passworduser"
            placeholder="********"
            help="min 8 caracteres"
            validation="required|min:8"
            class="grow"
          />
        </label>
        <!-- Enlace para recuperar la contraseña -->
        <label class="label">
          <RouterLink class="hover:underline" :to="{ name: 'reset' }">
        
            <p  class="label-text-alt link link-hover">¿Olvidaste tu contraseña?</p>
        </RouterLink>
        </label>
      </div>

      <!-- Botón de Inicio de Sesión -->
      <div class="form-control mt-6">
        <button class="btn btn-primary" type="submit">Inicia Sesión</button>
      </div>

      <!-- Enlace para Registrarse -->
      <div class="mt-6 text-center">
        <RouterLink class="hover:underline" :to="{ name: 'register' }">
          ¡Regístrate aquí!
        </RouterLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useLoginForm } from '@/modules/auth/composables/useLoginForm';
import { RouterLink } from 'vue-router';
import { FormKit } from '@formkit/vue';

const { emailuser, passworduser, onLogin, loading } = useLoginForm();
</script>


