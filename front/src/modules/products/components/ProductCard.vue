<template>
  <div class="flex bg-base-100 shadow-lg rounded-lg overflow-hidden mb-4 h-[350px]">
    <!-- Imagen del producto -->
    <div class="w-1/3">
      <img
        class="w-full h-full object-cover"
        :src="product.image_offer"
        :alt="product.title_offer"
      />
    </div>

    <!-- Contenido de la tarjeta -->
    <div class="w-2/3 p-4 flex flex-col justify-between">
      <!-- Sección del tiempo en la esquina superior derecha -->
      <div class="flex justify-between">
        <span class="text-sm text-gray-400 tooltip tooltip-bottom" data-tip="Fecha de fin oferta">
          <font-awesome-icon icon="clock" />
          {{ formattedTime }}
        </span>

        <span class="text-sm text-gray-400 ml-auto tooltip tooltip-bottom" data-tip="Categoria"
          ><font-awesome-icon icon="tag" /> {{ product.category_name }}</span
        >
        <span class="text-sm text-gray-400 ml-auto tooltip tooltip-bottom" data-tip="Empresa"
          ><font-awesome-icon icon="shop" /> {{ product.company_name }}</span
        >
      </div>
      <div class="divider mt-0 mb-0 h-0"></div>
      <h2 class="text-md font-bold text-neutral line-clamp-1">{{ product.title_offer }}</h2>
      <!-- Precio -->
      <div class="text-2xl font-semibold text-orange-500 mb-2">
        {{ product.new_price_offer }} €
        <span class="line-through text-gray-500 text-lg ml-2"
          >{{ product.original_price_offer }}€</span
        >
        <span class="text-lg text-primary">(-{{ discountPercentage }}%)</span>
      </div>
      <!-- Descripción del producto -->
      <p class="text-sm text-neutral line-clamp-2">{{ product.description_offer }}</p>

      <!-- Sección acciones -->
      <template v-if="authStore.isAuthenticated">
        <div class="flex justify-end mt-2">
          <!-- Botón para ver la oferta -->
          <router-link
            :to="{ name: 'OfferDetail', params: { offerId: product.id } }"
            class="btn btn-warning w-40"
          >
            Ver oferta
          </router-link>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { computed } from 'vue';
import { useAuthStore } from '@/modules/auth/composables/useAuthAction';
const authStore = useAuthStore();

const props = defineProps({
  product: {
    type: Object,
    required: true,
    default: () => ({
      title_offer: 'Título del producto',
      description_offer: 'Descripción corta del producto...',
      new_price_offer: '70.95',
      original_price_offer: '100.95',
      image_offer: 'https://via.placeholder.com/150',
      web_offer: 'https://due-home.com/aparadores-de-comedor/aparador-baltik.html',
      start_date: '01/01/2000',
      end_date: '01/01/2000',
      category_name: '',
      company_name: '',
    }),
  },
});
const discountPercentage = computed(() => {
  if (props.product.new_price_offer <= 0 || props.product.original_price_offer <= 0) return 0;
  return Math.round(
    ((props.product.original_price_offer - props.product.new_price_offer) /
      props.product.original_price_offer) *
      100,
  );
});

const formattedTime = computed(() => {
  const endDate = new Date(props.product.end_date);

  const day = String(endDate.getDate()).padStart(2, '0');
  const month = String(endDate.getMonth() + 1).padStart(2, '0'); 
  const year = endDate.getFullYear();

  return `${day}/${month}/${year}`;
});
</script>

<style scoped>
.h-40 {
  height: 12rem;
}
</style>
