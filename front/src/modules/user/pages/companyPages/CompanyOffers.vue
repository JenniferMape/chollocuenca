<template>
  <section class="bg-base-100 shadow-md p-6 rounded-lg ml-6 min-h-screen w-full">
    <div class="flex justify-between items-center mb-6">
      <h2 class="font-bold text-xl">Mis Ofertas</h2>
      <button @click="goToNewOffer" class="btn btn-info">Subir Nueva Oferta</button>
    </div>

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
          <h3 class="font-bold text-lg mb-2 line-clamp-2 h-[3rem]">
            {{ offer.title_offer }}
          </h3>

          <p class="text-sm text-gray-600 mb-2">Categoría: {{ offer.category_offer_name }}</p>

          <p class="text-sm text-gray-600 mb-4">
            Fecha: {{ offer.start_date_offer }} - {{ offer.end_date_offer }}
          </p>
        </div>

        <router-link
          :to="{ name: 'formOffer', params: { offerId: offer.id } }"
          class="btn btn-info"
        >
          Editar Oferta
        </router-link>
      </div>
    </div>

    <div v-else class="text-center text-gray-500 mt-10">No has subido ninguna oferta todavía.</div>

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
const companyId = authStore.user?.id;

const offers = ref([]);
let categoryMap = {};
const currentPage = ref(1);
const offersPerPage = 6;


function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  });
}

const loadCategories = async () => {
  try {
    const response = await tesloApi.get('/category'); 
    if (response.data.status === 200) {
      categoryMap = response.data.result.reduce((map, category) => {
        map[category.id] = category.name_category;
        return map;
      }, {});
    } else {
      console.error('Error al obtener las categorías:', response.data.message);
    }
  } catch (error) {
    console.error('Error al obtener categorías:', error.response?.data || error.message);
  }
};

const getOffersByCompany = async () => {
  if (!Object.keys(categoryMap).length) {
    await loadCategories();
  }

  try {
    const response = await tesloApi.get(`/offer?filter=type:offer_company:${companyId}`);
    if (response.data.status === 200) {
      offers.value = response.data.result.map((offer) => ({
        ...offer,
        start_date_offer: formatDate(offer.start_date_offer),
        end_date_offer: formatDate(offer.end_date_offer),
        category_offer_name: categoryMap[offer.id_category_offer] || 'Categoría desconocida',
      }));
    } else {
      console.error('Error al obtener las ofertas:', response.data.message);
    }
  } catch (error) {
    console.error('Error al obtener ofertas:', error.response?.data || error.message);
  }
};


const paginatedOffers = computed(() => {
  const start = (currentPage.value - 1) * offersPerPage;
  return offers.value.slice(start, start + offersPerPage);
});


const totalPages = computed(() => Math.ceil(offers.value.length / offersPerPage));


const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

onMounted(async () => {
  await loadCategories();
  await getOffersByCompany();
});


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
const goToNewOffer = () => {
  router.push({ name: 'formOffer' });
};
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>
