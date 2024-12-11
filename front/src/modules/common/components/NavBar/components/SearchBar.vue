<!-- SearchBar.vue -->
<template>
  <div class="relative flex-grow max-w-md mx-auto">
    <label class="input input-bordered flex items-center gap-2 w-full">
      <input
        v-model="query"
        type="text"
        placeholder="Buscar ofertas..."
        class="input input-bordered"
        @keydown.enter="emitSearch"
      />
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 16 16"
        fill="currentColor"
        class="absolute left-2 h-4 w-4 opacity-70"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
          clip-rule="evenodd"
        />
      </svg>
    </label>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';

// Estado reactivo para el input
const query = ref('');

// Definir los eventos que se van a emitir
const emit = defineEmits<{
  (event: 'update:modelValue', value: string): void;
}>();

// Emitir el valor al presionar Enter
const emitSearch = () => {
  emit('update:modelValue', query.value); // Emite el valor de búsqueda
};
</script>
