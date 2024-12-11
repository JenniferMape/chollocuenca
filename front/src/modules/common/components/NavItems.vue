<template>
  <nav class="w-full py-4 shadow-lg bg-base-100 text-base-content">
    <div class="container mx-auto flex justify-center items-center">
      <div class="flex space-x-4 text-sm font-medium">
        <button
          v-for="item in navItems"
          :key="item.id"
          @click="selectCategory(item.id)"
          :class="[
            'px-3 py-2 rounded-lg transition text-primary',
            selectedCategoryId === item.id
              ? 'bg-primary text-white'
              : 'hover:bg-primary hover:text-white',
          ]"
        >
          {{ item.text }}
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'; // Importar computed
import { useCategoryStore } from '@/modules/landing/stores/categoryStore';
import { useRouter } from 'vue-router';

const categoryStore = useCategoryStore();
const router = useRouter();

// Usar computed para obtener el selectedCategoryId reactivo
const selectedCategoryId = computed(() => categoryStore.selectedCategoryId);

const navItems = [
  { id: 0, text: 'Ofertas Destacadas' },
  { id: 1, text: 'Alimentación' },
  { id: 2, text: 'Automoción' },
  { id: 3, text: 'Electrónica' },
  { id: 4, text: 'Hogar' },
  { id: 5, text: 'Moda' },
  { id: 6, text: 'Ocio y cultura' },
  { id: 7, text: 'Salud' },
];

function selectCategory(id: number) {
  // Actualiza la categoría seleccionada en el store
  categoryStore.setCategory(id);

  // Redirige a la página principal si estás en otra página
  if (router.currentRoute.value.name !== 'home') {
    router.push({ name: 'home' });
  }
}
</script>
