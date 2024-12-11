<template>
  <!-- Formulario de Registro -->
  <div class="card bg-base-100 w-full shadow-2xl">
    <form class="card-body p-6 grid grid-cols-1 gap-2" @submit.prevent="registerUser">
      <h2 class="card-title text-lg mb-2 col-span-full text-neutral">¡Te estábamos esperando!</h2>

      <!-- Checkbox para seleccionar cuenta de empresa -->
      <div class="form-control col-span-full">
        <label class="label cursor-pointer">
          <span class="label-text text-neutral">¿Va a ser una cuenta de empresa?</span>
          <input
            type="checkbox"
            id="typeUser"
            v-model="typeUser"
            class="checkbox checkbox-primary"
          />
        </label>
      </div>

      <!-- Campo CIF (visible si es cuenta de empresa) -->
      <div class="form-control col-span-full" v-if="typeUser">
        <label class="label">
          <span class="label-text text-neutral">CIF</span>
        </label>
        <FormKit
          type="text"
          name="cif"
          v-model="cif"
          placeholder="A123456"
          validation="required|regex:/^[A-Z0-9]+$/"
        />
      </div>

      <!-- Nombre de usuario -->
      <div class="form-control col-span-full">
        <label class="label">
          <span class="label-text text-neutral">Nombre de usuario</span>
        </label>
        <FormKit
          type="text"
          name="nombre"
          v-model="username"
          placeholder="Nombre de usuario"
          validation="required"
        />
      </div>

      <!-- Email -->
      <div class="form-control col-span-full">
        <label class="label">
          <span class="label-text text-neutral">Email</span>
        </label>
        <FormKit
          type="email"
          name="email"
          prefix-icon="email"
          v-model="email"
          placeholder="ejemplo@ejemplo.com"
          validation="required|email"
        />
      </div>

      <!-- Contraseña -->
      <div class="form-control col-span-full">
        <label class="label">
          <span class="label-text text-neutral">Contraseña</span>
        </label>
        <FormKit
          type="password"
          name="contraseña"
          prefix-icon="password"
          v-model="password"
          placeholder="Contraseña"
          validation="required"
        />
      </div>

      <!-- Confirmar contraseña -->
      <div class="form-control col-span-full">
        <label class="label">
          <span class="label-text text-neutral">Confirma la contraseña</span>
        </label>
        <FormKit
          type="password"
          name="contraseña"
          prefix-icon="password"
          v-model="password2"
          placeholder="Confirma la contraseña"
          validation="required|same:password"
        />
      </div>

      <!-- Botón de Registro -->
      <div class="form-control col-span-full mt-2">
        <button class="btn btn-primary w-full" type="submit">¡Regístrate ahora!</button>
      </div>

      <!-- Enlace para Iniciar Sesión -->
      <div class="mt-2 text-center col-span-full">
        <RouterLink class="hover:underline" :to="{ name: 'login' }">
          ¿Ya tienes cuenta?
        </RouterLink>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { useRegisterForm } from '@/modules/auth/composables/useRegisterForm';

const { typeUser, cif, username, email, password, password2, registerUser } = useRegisterForm();
</script>
