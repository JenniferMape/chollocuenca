// src/stores/categoryStore.ts
import { defineStore } from 'pinia';

export const useCategoryStore = defineStore('category', {
  state: () => ({
    selectedCategoryId: 0, // Por defecto, categoría sin filtro
  }),
  actions: {
    setCategory(id: number) {
      this.selectedCategoryId = id;
    },
  },
});
