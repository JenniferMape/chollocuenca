<template>
  <header class="shadow">
    <NavBar>
      <template #logo>
        <LogoNav />
      </template>
      <template #search-bar>
        <div class="flex-grow hidden lg:block"></div>
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
  <div class="container mx-auto p-4 relative">
    <!-- Tarjeta de la oferta -->
    <div class="card bg-base-100 shadow-xl">
      <!-- Botón de volver en la esquina superior izquierda -->
      <button
        @click="goBack"
        class="btn btn-ghost absolute top-6 left-6 text-secondary text-sm font-bold flex items-center z-10"
      >
        <!-- Icono de Font Awesome -->
        <font-awesome-icon icon="arrow-left" class="h-5 w-5 mr-1" />
        Volver
      </button>

      <div class="card-actions absolute top-6 right-6 flex items-center z-10">
        <div
          v-if="authStore.isClient"
          class="tooltip"
          :data-tip="isFavorite ? '¡Oferta favorita!' : 'Agregar a favoritos'"
        >
          <button
            @click="toggleFavorite"
            class="btn btn-ghost text-secondary text-sm font-bold flex items-center"
            :class="{ 'text-red-500': isFavorite, 'text-gray-500': !isFavorite }"
            aria-label="Marcar como favorita"
          >
            <!-- Icono de Font Awesome -->
            <font-awesome-icon :icon="[isFavorite ? 'fas' : 'far', 'heart']" class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Imagen principal -->
      <figure class="p-4 flex justify-center">
        <img
          :src="offerImageUrl || 'default-image.jpg'"
          alt="Imagen de la oferta"
          class="rounded-lg object-cover max-w-xs max-h-48"
        />
      </figure>

      <!-- Información de la oferta -->
      <div class="card-body p-6">
        <h2 class="card-title text-2xl text-neutral font-bold">{{ offerTitle }}</h2>

        <div class="text-2xl font-semibold text-orange-500 mb-2">
          {{ newPrice }}€
          <span class="line-through text-neutral text-lg ml-2">{{ originalPrice }} €</span>
          <span class="text-lg text-primary">(-{{ discountPercentage }}%)</span>
        </div>

        <!-- Botón para ir al enlace de la oferta -->
        <div class="card-actions mt-4">
          <a
            :href="offerWebsite"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-accent w-full"
            :class="{ 'btn-disabled': !offerWebsite }"
            :aria-disabled="!offerWebsite"
          >
            Ir al chollo
          </a>
        </div>
        <div class="flex justify-between mt-4 gap-4">
          <!-- Fecha de comienzo -->
          <span
            class="text-sm text-gray-400 tooltip tooltip-bottom"
            data-tip="Fecha de comienzo oferta"
          >
            <font-awesome-icon icon="calendar-day" class="mr-1" />
            {{ formattedStartDate }}
          </span>

          <!-- Fecha de fin -->
          <span class="text-sm text-gray-400 tooltip tooltip-bottom" data-tip="Fecha de fin oferta">
            <font-awesome-icon icon="clock" class="mr-1" />
            {{ formattedEndDate }}
          </span>

          <!-- Categoría -->
          <span class="text-sm text-gray-400 tooltip tooltip-bottom" data-tip="Categoría">
            <font-awesome-icon icon="tag" class="mr-1" />
            {{ offerCategoryLabel }}
          </span>

          <!-- Empresa -->
          <span class="text-sm text-gray-400 tooltip tooltip-bottom" data-tip="Empresa">
            <font-awesome-icon icon="shop" class="mr-1" />
            {{ offerCompany }}
          </span>
        </div>

        <div class="divider mt-0 mb-0 h-0"></div>
        <!-- Detalles adicionales de la oferta -->
        <div class="bg-base-200 rounded-lg p-4 mt-6">
          <h3 class="font-semibold text-lg">Descripción de la oferta</h3>
          <p class="text-neutral mt-2">{{ offerDescription }}</p>
        </div>

        <!-- Información adicional -->
        <div class="grid grid-cols-2 gap-4 mt-6">
          <!-- Código de descuento -->
          <div class="p-4 border border-gray-300 rounded-lg bg-base-200 shadow-sm">
            <h4 class="font-semibold text-md text-secondary mb-2">Código de descuento:</h4>
            <div class="flex items-center space-x-2">
              <p class="text-lg text-primary font-medium">
                {{ discountCode || 'No disponible' }}
              </p>
              <button
                v-if="discountCode"
                @click="copyToClipboard(discountCode)"
                class="btn btn-sm btn-secondary btn-ghost tooltip tooltip-bottom"
                data-tip="Copiar código"
                aria-label="Copiar código"
              >
                <font-awesome-icon icon="clipboard" />
              </button>
            </div>
          </div>

          <!-- Dirección -->
          <div class="p-4 border border-gray-300 rounded-lg bg-base-200 shadow-sm">
            <h4 class="font-semibold text-md text-secondary mb-2">Dirección:</h4>
            <p class="text-sm text-secondary font-medium">
              {{ offerAddress || 'No disponible' }}
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// ------------------------
// Importaciones
// ------------------------
import { ref, onMounted, watch, nextTick, onUnmounted } from 'vue';
import { useAuthStore } from '@/modules/auth/stores/auth.store';
import { useToast } from 'vue-toastification';
import { tesloApi } from '@/api/tesloApi';
import { useRouter } from 'vue-router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import type { Offer } from '@/modules/products/interfaces/product.interface';

// Componentes
import NavBar from '@/modules/common/components/NavBar/NavBar.vue';
import LogoNav from '@/modules/common/components/NavBar/components/LogoNav.vue';
import AuthButtons from '@/modules/common/components/NavBar/components/AuthButtons/AuthButtons.vue';
import ThemeToggle from '@/modules/common/components/NavBar/components/ThemeToggle.vue';
import NavItems from '@/modules/common/components/NavItems.vue';

// ------------------------
// Definición de Props
// ------------------------
const props = defineProps({
  offerId: {
    type: Number,
    required: true,
  },
});
// Setup variables
const authStore = useAuthStore();
const router = useRouter();
// ------------------------
// Datos reactivos
// ------------------------
const offerTitle = ref<string>('');
const offerCategoryLabel = ref<string>('');
const formattedStartDate = ref<string>('');
const formattedEndDate = ref<string>('');
const newPrice = ref<number>(0);
const originalPrice = ref<number>(0);
const discountPercentage = ref<number>(0);
const offerDescription = ref<string>('');
const discountCode = ref<string>('');
const offerImageUrl = ref<string | null>(null);
const offerWebsite = ref<string>('');
const offerCompany = ref<string>('');
const offerAddress = ref<string>('');
const isFavorite = ref<boolean>(false);
const userId = authStore.user?.id;
const favoriteId = ref<number | null>(null);
const toast = useToast();
// ------------------------
// Métodos
// ------------------------
const loadOfferData = async () => {
  try {
    const response = await tesloApi.get(`/offer?filter=type:offer_id:${props.offerId}`);
    const offer: Offer = response.data.result;

    offerTitle.value = offer.title_offer || 'Título no disponible';
    offerCategoryLabel.value = offer.category_name || 'Categoría no disponible';
    formattedStartDate.value = formatDate(offer.start_date_offer);
    formattedEndDate.value = formatDate(offer.end_date_offer);
    newPrice.value = offer.new_price_offer || 0;
    originalPrice.value = offer.original_price_offer || 0;
    discountPercentage.value = calculateDiscountPercentage(
      offer.new_price_offer,
      offer.original_price_offer,
    );
    offerDescription.value = offer.description_offer || 'Descripción no disponible';
    discountCode.value = offer.discount_code_offer || 'Sin código disponible';
    offerImageUrl.value = offer.image_offer ? offer.image_offer : null;
    offerWebsite.value = offer.web_offer || 'Sitio web no disponible';
    offerAddress.value = offer.address_offer || 'Dirección no disponible';
    offerCompany.value = offer.company_name || 'Dirección no disponible';
  } catch (error) {
    toast.error('Error al cargar los datos de la oferta');
  }
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES');
};

const calculateDiscountPercentage = (newPrice: number, originalPrice: number): number => {
  if (newPrice <= 0 || originalPrice <= 0) return 0;
  return Math.round(((originalPrice - newPrice) / originalPrice) * 100);
};
// Función para volver atrás
const goBack = () => {
  router.back(); 
};

// ------------------------
// Ciclo de vida
// ------------------------
onMounted(async () => {
  await nextTick();
  window.scrollTo({ top: 0, behavior: 'smooth' });
  loadOfferData();
  checkIfFavorite();
});

onUnmounted(() => {
  resetData();
});

// ------------------------
// Observadores
// ------------------------
watch(
  () => props.offerId,
  async (newOfferId, oldOfferId) => {
    if (newOfferId !== oldOfferId) {
      await nextTick();
      window.scrollTo({ top: 0 });
      resetData();
      loadOfferData();
      checkIfFavorite();
    }
  },
);

// ------------------------
// Métodos adicionales
// ------------------------
const resetData = () => {
  offerTitle.value = '';
  offerCategoryLabel.value = '';
  formattedStartDate.value = '';
  formattedEndDate.value = '';
  newPrice.value = 0;
  originalPrice.value = 0;
  discountPercentage.value = 0;
  offerDescription.value = '';
  discountCode.value = '';
  offerImageUrl.value = null;
  offerWebsite.value = '';
  offerAddress.value = '';
};

const copyToClipboard = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text);
    toast.success('¡Código copiado al portapapeles!');
  } catch (error) {
    toast.error('No se pudo copiar el código. Inténtalo de nuevo.');
  }
};
// ------------------------
// Manejo de Favoritos
// ------------------------
const checkIfFavorite = async () => {
  try {
    const response = await tesloApi.get(`/favorite/${userId}`);
    const favorites = response.data.result; 

    const favorite = favorites.find(
      (fav: { id_offer_favorite: number }) => fav.id_offer_favorite === Number(props.offerId),
    );

    if (favorite) {
      isFavorite.value = true;
      favoriteId.value = favorite.id; 
    } else {
      isFavorite.value = false;
      favoriteId.value = null;
    }
  } catch (error) {
    console.error('Error al verificar si es favorito:', error);
  }
};

const toggleFavorite = async () => {
  try {
    const favoriteData = {
      id_user_favorite: Number(userId),
      id_offer_favorite: Number(props.offerId),
    };

    const response = await tesloApi.post(`/favorite`, favoriteData);
    const { status, result, message } = response.data;

    if (status === 201) {
      // Favorito agregado
      isFavorite.value = true;
      favoriteId.value = result.id; 
    } else if (status === 200) {
      // Favorito eliminado
      isFavorite.value = false;
      favoriteId.value = null; 
    } else {
      console.warn('Respuesta inesperada del servidor:', response.data);
    }
  } catch (error) {
    console.error('Error al procesar favorito:', error);
  }
};
</script>
