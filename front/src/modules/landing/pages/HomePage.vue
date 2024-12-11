<template>
  <header class="shadow">
    <NavBar>
      <template #logo>
        <LogoNav />
      </template>
      <template #search-bar>
        <div class="relative flex-grow max-w-md mx-auto">
          <label class="input input-bordered flex items-center gap-2 w-full">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Buscar ofertas..."
              class="w-full px-3 py-2 text-sm rounded"
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

      <template #auth-buttons>
        <AuthButtons />
      </template>

      <template #theme-toggle>
        <ThemeToggle />
      </template>
    </NavBar>
    <NavItems />
  </header>

  <main>
    <div class="container mx-auto p-4">
      <!-- Mostrar mensaje cuando no hay ofertas -->
      <div v-if="noOffers" class="flex flex-col items-center text-gray-500 p-4">
        <img src="@/assets/lupa-no-encontrada.svg" alt="No hay ofertas" class="w-32 h-32 mb-4" />
        <p>No hay ofertas disponibles para esta categoría.</p>
      </div>

      <!-- Mostrar lista de ofertas cuando hay productos -->
      <ProductCard v-else v-for="product in filteredOffers" :key="product.id" :product="product" />
    </div>
  </main>
</template>
<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import NavBar from '@/modules/common/components/NavBar/NavBar.vue';
import LogoNav from '@/modules/common/components/NavBar/components/LogoNav.vue';
import AuthButtons from '@/modules/common/components/NavBar/components/AuthButtons/AuthButtons.vue';
import ThemeToggle from '@/modules/common/components/NavBar/components/ThemeToggle.vue';
import NavItems from '@/modules/common/components/NavItems.vue';
import ProductCard from '@/modules/products/components/ProductCard.vue';
import { tesloApi } from '@/api/tesloApi';
import { useCategoryStore } from '@/modules/landing/stores/categoryStore';
import type { Offer } from '@/modules/products/interfaces/product.interface';

const products = ref<Offer[]>([]);
const categoryStore = useCategoryStore();
const noOffers = ref(false);
const searchQuery = ref('');

// Función para cargar ofertas
const loadOffers = async () => {
  const category = categoryStore.selectedCategoryId;
  const endpoint = category > 0 ? `/offer?filter=type:offer_category:${category}` : `/offer`;

  try {
    const response = await tesloApi.get(endpoint);
    products.value = response.data.result.map((offer: Offer) => ({
      id: offer.id,
      title_offer: offer.title_offer,
      description_offer: offer.description_offer,
      new_price_offer: offer.new_price_offer,
      original_price_offer: offer.original_price_offer,
      image_offer: offer.image_offer,
      web_offer: offer.web_offer,
      end_date: offer.end_date_offer,
      category_name: offer.category_name,
      company_name: offer.company_name,
    }));
    noOffers.value = products.value.length === 0;
  } catch (error: any) {
    if (error.response && error.response?.status === 404) {
      noOffers.value = true;
      products.value = [];
    } else {
      console.error('Error fetching offers:', error);
    }
  }
};

// Filtrar ofertas por búsqueda y categoría
const filteredOffers = computed(() => {
  return products.value.filter((product) => {
    const searchMatch =
      product.title_offer.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      product.description_offer.toLowerCase().includes(searchQuery.value.toLowerCase());
    return searchMatch;
  });
});

// Observa cambios en selectedCategoryId para recargar las ofertas
watch(() => categoryStore.selectedCategoryId, loadOffers);

// Carga inicial de ofertas
loadOffers();
</script>
