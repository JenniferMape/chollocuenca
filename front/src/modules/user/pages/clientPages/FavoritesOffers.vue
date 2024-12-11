<template>
  <!-- User's Offers -->
  <section class="w-full bg-base-100 shadow-md p-6 rounded-lg ml-6 min-h-screen">
    <div v-if="paginatedOffers.length" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="offer in paginatedOffers"
        :key="offer.id"
        class="bg-base-200 p-4 rounded-lg shadow-md flex flex-col justify-between"
      >
        <!-- Imagen -->
        <img
          :src="offer.image_offer || 'default-image.jpg'"
          alt="Imagen de la oferta"
          class="w-full h-40 object-cover rounded-md mb-4"
        />

        <!-- Contenido de la Oferta -->
        <div class="flex-grow">
          <!-- Título -->
          <h3 class="font-bold text-lg mb-2 line-clamp-2 h-[3.5rem]">
            {{ offer.title_offer }}
          </h3>
          <!-- Fecha -->
          <p class="text-sm text-gray-600 mb-4">
            Fecha: {{ offer.start_date_offer }} - {{ offer.end_date_offer }}
          </p>
        </div>

        <!-- Botón -->
        <router-link
          :to="{ name: 'OfferDetail', params: { offerId: offer.id } }"
          class="btn btn-accent btn-sm mt-auto"
        >
          Ver oferta
        </router-link>
      </div>
    </div>

    <div v-else class="text-center text-gray-500 mt-10">No has subido ninguna oferta todavía.</div>

    <!-- Pagination Controls -->
    <div v-if="offers.length > offersPerPage" class="flex justify-between mt-6">
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="btn btn-secondary"
      >
        Anterior
      </button>
      <span>Página {{ currentPage }} de {{ totalPages }}</span>
      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="btn btn-secondary"
      >
        Siguiente
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { tesloApi } from '@/api/tesloApi';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/modules/auth/composables/useAuthAction';

const authStore = useAuthStore();
const userId = authStore.user?.id;

// Variables para manejar las ofertas, categorías y la paginación
const offers = ref([]);
const currentPage = ref(1);
const offersPerPage = 6;

// Función para formatear fecha a día/mes/año
function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
}

// Función para obtener las ofertas de la compañía y procesarlas
const getOffersByCompany = async () => {
  try {
    const response = await tesloApi.get(`/favorite/${userId}`, {
      params: { details: true },
    });

    if (response.data.status === 200) {
      offers.value = response.data.result.map((offer) => ({
        ...offer,
        start_date_offer: formatDate(offer.start_date_offer),
        end_date_offer: formatDate(offer.end_date_offer),
      }));
    } else {
      console.error('Error al obtener las ofertas:', response.data.message);
    }
  } catch (error) {
    console.error('Error al verificar si es favorito:', error);
  }
};

// Computed para las ofertas paginadas
const paginatedOffers = computed(() => {
  const start = (currentPage.value - 1) * offersPerPage;
  return offers.value.slice(start, start + offersPerPage);
});

// Computed para el total de páginas
const totalPages = computed(() => Math.ceil(offers.value.length / offersPerPage));

// Cambiar de página
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Llamar a la función para obtener las ofertas y categorías al montar el componente
onMounted(async () => {
  await getOffersByCompany();
});

// Observar cambios en la ruta para actualizar las ofertas al volver a esta página
const route = useRoute();
watch(
  () => route.path,
  async (newPath, oldPath) => {
    if (newPath !== oldPath) {
      await getOffersByCompany();
    }
  },
);

const router = useRouter();
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>
