<template>
  <section class="w-3/4 bg-base-100 shadow-md p-6 rounded-lg ml-6">
    <h2 class="font-bold text-xl mb-6">{{ isEditMode ? 'Editar Oferta' : 'Nueva Oferta' }}</h2>
    <form @submit.prevent="handleOfferAction" class="card-body p-6 grid grid-cols-1 gap-4">
      <!-- Campos del formulario -->
      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Título de la oferta</span>
        </label>
        <FormKit
          v-model="offerTitle"
          type="text"
          name="title"
          placeholder="Ej: Descuento en ropa"
          validation="required"
        />
      </div>

      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Categoría de la oferta</span>
        </label>
        <FormKit
          v-model="offerCategory"
          :options="categories"
          type="select"
          validation="required"
          placeholder="Selecciona una categoría"
        />
      </div>
      <FormKit type="group">
      <div class="flex gap-4 mb-4">
        <div class="form-control">
          <label class="label">
            <span class="label-text">Fecha de inicio</span>
          </label>
          <FormKit v-model="startDate" type="date" name="startDate" validation="required" />
        </div>

        <div class="form-control">
          <label class="label">
            <span class="label-text">Fecha de finalización</span>
          </label>
          <FormKit v-model="endDate" type="date" name="endDate"  validation="date_after_node:startDate" help="La fecha de fin debe ser posterior a la de inicio"  message=" 'La fecha de fin debe ser después de la fecha de inicio.'"/>
        </div>
      </div>
    </FormKit>
      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Precio original</span>
        </label>
        <FormKit
          v-model="oldPrice"
          type="number"
          placeholder="Ej: 10.00€"
          min="0"
          step="0.10"
          validation="required"
        />
      </div>

      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Precio con descuento</span>
        </label>
        <FormKit
          v-model="newPrice"
          type="number"
          placeholder="Ej: 8.00€"
          min="0"
          step="0.10"
          validation="required"
        />
      </div>

      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Descripción de la oferta</span>
        </label>
        <FormKit
          v-model="offerDescription"
          type="textarea"
          placeholder="Escribe una descripción detallada de la oferta"
          rows="4"
          validation="required"
        />
      </div>

      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Código de descuento</span>
        </label>
        <FormKit v-model="discountCode" type="text" placeholder="Ej: DESCUENTO10" />
      </div>

      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Imagen de la oferta</span>
        </label>

        <!-- Contenedor centrado para la vista previa de la imagen -->
        <div v-if="offerImageUrl" class="flex justify-center mb-4">
          <img
            :src="offerImageUrl"
            alt="Vista previa de la imagen de la oferta"
            class="w-40 h-40 object-cover rounded-md shadow-md"
          />
        </div>

        <div class="flex mt-4 gap-2">
          <button @click="triggerFileInput" class="btn btn-outline btn-secondary w-1/2">
            Cambiar Imagen
          </button>
          <button
            :disabled="!offerImageUrl"
            @click="removeImage"
            class="btn btn-error btn-outline w-1/2"
          >
            Eliminar Imagen
          </button>
        </div>

        <!-- Input de archivo para subir la imagen -->
        <input type="file" @change="handleImageChange" ref="offerImageInput" class="hidden" />
      </div>
      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Página web de la oferta</span>
        </label>
        <FormKit v-model="offerWebsite" type="url" placeholder="Ej: https://ejemplo.com" />
      </div>
      <div class="form-control mb-4">
        <label class="label">
          <span class="label-text">Dirección de la oferta</span>
        </label>
        <FormKit v-model="offerAddress" type="text" placeholder="Ej: Calle Falsa 123" />
      </div>

      <!-- Botón para crear o actualizar oferta -->
      <div class="flex mt-4 gap-2">
        <button type="submit" class="btn btn-info w-1/2">
          {{ isEditMode ? 'Guardar Cambios' : 'Publicar Oferta' }}
        </button>
        <button v-if="isEditMode" @click="deleteOffer" class="btn btn-error w-1/2">
          Eliminar Oferta
        </button>
      </div>
    </form>
  </section>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
import { useToast } from 'vue-toastification';
import { tesloApi } from '@/api/tesloApi';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/modules/auth/composables/useAuthAction';

const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const offerTitle = ref('');
const offerCategory = ref('');
const startDate = ref('');
const endDate = ref('');
const newPrice = ref('');
const oldPrice = ref('');
const offerDescription = ref('');
const discountCode = ref('');
const offerImage = ref(null);
const offerImageUrl = ref(null);
const offerImageInput = ref(null);
const offerWebsite = ref('');
const offerAddress = ref('');
const id = authStore.user?.id;


const categories = [
  { value: '1', label: 'Alimentación' },
  { value: '2', label: 'Automoción' },
  { value: '3', label: 'Electrónica' },
  { value: '4', label: 'Hogar' },
  { value: '5', label: 'Moda' },
  { value: '6', label: 'Ocio y Cultura' },
  { value: '7', label: 'Salud' },
];

// Props
const props = defineProps({
  offerId: {
    type: String,
    default: null,
  },
  isEditMode: {
    type: Boolean,
    default: false,
  },
});

const isEditMode = ref(props.isEditMode);

watch(
  () => props.isEditMode,
  (newIsEditMode) => {
    isEditMode.value = newIsEditMode;
  },
);


watch(
  () => props.offerId,
  (newOfferId, oldOfferId) => {
    if (newOfferId && newOfferId !== oldOfferId) {
      loadOfferData();
    } else if (!newOfferId) {
      resetForm();
    }
  },
);

onMounted(() => {
  if (props.offerId) {
    loadOfferData();
  } else {
    //resetForm(); 
  }
});
onUnmounted(() => {
  resetForm();
});

const resetForm = () => {
  offerTitle.value = '';
  offerCategory.value = '';
  startDate.value = '';
  endDate.value = '';
  oldPrice.value = '';
  newPrice.value = '';
  offerDescription.value = '';
  discountCode.value = '';
  offerImageUrl.value = null;
  offerWebsite.value = '';
  offerAddress.value = '';
  offerImage.value = null;
};

function triggerFileInput() {
  // Activa el input de archivo
  offerImage.value = null;
  offerImageUrl.value = null;
  offerImageInput.value.click();
}

function handleImageChange(event) {
  const file = event.target.files[0];
  if (file) {
    offerImage.value = file;
    offerImageUrl.value = URL.createObjectURL(file);
  }
}

function removeImage() {
  offerImage.value = null;
  offerImageUrl.value = null;
}

const loadOfferData = async () => {
  try {
    const response = await tesloApi.get(`/offer?filter=type:offer_id:${props.offerId}`);
    const offer = response.data.result;

    if (offer.title_offer !== null) offerTitle.value = offer.title_offer;
    if (offer.id_category_offer !== null) offerCategory.value = offer.id_category_offer;
    if (offer.start_date_offer !== null) startDate.value = offer.start_date_offer.split(' ')[0]; // Solo toma la fecha (yyyy-MM-dd)
    if (offer.end_date_offer !== null) endDate.value = offer.end_date_offer.split(' ')[0];
    if (offer.new_price_offer !== null) newPrice.value = offer.new_price_offer;
    if (offer.original_price_offer !== null) oldPrice.value = offer.original_price_offer;
    if (offer.description_offer !== null) offerDescription.value = offer.description_offer;
    if (offer.discount_code_offer !== null) discountCode.value = offer.discount_code_offer;
    if (offer.web_offer !== null) offerWebsite.value = offer.web_offer;
    if (offer.address_offer !== null) offerAddress.value = offer.address_offer;
    if (offer.image_offer !== null) offerImageUrl.value = offer.image_offer;
  } catch (error) {
    toast.error('Error al cargar los datos de la oferta');
  }
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return date.toISOString().slice(0, 19).replace('T', ' ');
};

const handleOfferAction = () => {
  isEditMode.value ? updateOfferPayload() : createOfferPayload();
};

const createOfferPayload = async () => {
  if (
    !offerTitle.value ||
    !offerCategory.value ||
    !startDate.value ||
    !endDate.value ||
    !oldPrice.value ||
    !newPrice.value ||
    !offerDescription.value ||
    !offerWebsite.value
  ) {
    toast.error('Por favor, completa todos los campos obligatorios.');
    return;
  }

  const formData = new FormData();
  formData.append('id_company_offer', id);
  formData.append('id_category_offer', offerCategory.value);
  formData.append('title_offer', offerTitle.value);
  formData.append('new_price_offer', newPrice.value);
  formData.append('original_price_offer', oldPrice.value);
  formData.append('description_offer', offerDescription.value);
  formData.append('start_date_offer', formatDateTime(startDate.value));
  formData.append('end_date_offer', formatDateTime(endDate.value));
  formData.append('discount_code_offer', discountCode.value);
  formData.append('web_offer', offerWebsite.value);
  formData.append('address_offer', offerAddress.value);

  if (offerImage.value) {
    formData.append('image_offer', offerImage.value);
  }

  try {
    const response = await tesloApi.post('/offer', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    console.log(response.data.message);
    if (response.data.status === 201) {
      toast.success(response.data.message);
      router.push({ name: 'companyOffers' });
    } else {
      toast.error(`Error: ${response.data.error}`);
    }
  } catch (error) {
    toast.error('Hubo un error al enviar la oferta.');
  }
};

const updateOfferPayload = async () => {
  const formData = new FormData();
  formData.append('id', props.offerId);
  formData.append('id_company_offer', id);
  formData.append('id_category_offer', offerCategory.value);
  formData.append('title_offer', offerTitle.value);
  formData.append('new_price_offer', newPrice.value);
  formData.append('original_price_offer', oldPrice.value);
  formData.append('description_offer', offerDescription.value);
  formData.append('start_date_offer', formatDateTime(startDate.value));
  formData.append('end_date_offer', formatDateTime(endDate.value));
  formData.append('discount_code_offer', discountCode.value);
  formData.append('web_offer', offerWebsite.value);
  formData.append('address_offer', offerAddress.value);

  if (offerImage.value) {
    formData.append('image_offer', offerImage.value);
  }

  try {
    const response = await tesloApi.post('/offer', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    if (response.data.status === 200) {
      toast.success(response.data.message);
      router.push({ name: 'companyOffers' });
    } else {
      toast.error(`Error: ${response.data.error}`);
    }
  } catch (error) {
    toast.error('Hubo un error al editar la oferta.');
  }
};
const deleteOffer = async () => {
  try {
    const response = await tesloApi.delete(`/offer?id=${props.offerId}`);

    if (response.data.status === 200) {
      toast.success(response.data.message);
      router.push({ name: 'companyOffers' });
    } else {
      toast.error(`Error: ${response.data.error}`);
    }
  } catch (error) {
    toast.error('Hubo un error al editar la oferta.');
  }
};
</script>
