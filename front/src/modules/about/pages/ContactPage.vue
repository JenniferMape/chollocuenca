<template>
  <section class="relative flex flex-col flex-1 h-full">
    <!-- Mapa de Google -->
    <div class="absolute inset-0">
      <iframe
        width="100%"
        height="100%"
        frameborder="0"
        marginheight="0"
        marginwidth="0"
        title="map"
        scrolling="no"
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1815.3321612191226!2d-2.1438488044532225!3d40.075130735433355!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd5d67599ff68f7f%3A0x51dc89bc62923f49!2sCIFP%20N%C2%BA%201!5e0!3m2!1ses!2ses!4v1727893751467!5m2!1ses!2ses"
        class="rounded-lg shadow-lg"
      ></iframe>
    </div>

    <!-- Formulario de contacto -->
    <div class="container relative z-10 mx-auto flex items-center justify-center h-full py-12">
      <div class="bg-base-200 rounded-lg p-8 shadow-lg w-full max-w-lg">
        <h2 class="text-neutral text-lg mb-4 font-semibold title-font">Contáctanos</h2>
        <p class="leading-relaxed mb-5 text-sm text-gray-500">¿Sobre qué asunto nos contactas?</p>

        <div class="form-control mb-4">
          <label for="email" class="label">
            <span class="label-text text-sm text-neutral">Tu correo</span>
          </label>
          <input
            v-model="email"
            type="email"
            id="email"
            name="email"
            class="input input-bordered w-full"
            placeholder="ejemplo@correo.com"
          />
        </div>

        <div class="form-control mb-4">
          <label for="message" class="label">
            <span class="label-text text-sm text-neutral">Tu mensaje</span>
          </label>
          <textarea
            v-model="message"
            id="message"
            name="message"
            class="textarea textarea-bordered w-full h-32 resize-none"
            placeholder="Escribe tu mensaje aquí..."
          ></textarea>
        </div>

        <button
          :disabled="isLoading"
          @click="sendMessage"
          class="text-neutral btn btn-primary w-full"
        >
          {{ isLoading ? 'Enviando...' : 'Enviar mensaje' }}
        </button>
        <p class="text-xs text-gray-400 mt-3 text-center">
          Recuerda que te estás poniendo en contacto con <strong>CholloCuenca</strong>, no con
          alguna tienda.
        </p>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';

const email = ref('');
const message = ref('');
const isLoading = ref(false);

const sendMessage = async () => {
  if (!email.value || !message.value) {
    alert('Por favor, completa todos los campos.');
    return;
  }

  isLoading.value = true;
  try {
    const response = await axios.post('https://api.chollocuenca.site/contact', {
      email: email.value,
      message: message.value,
    });
    alert('Mensaje enviado con éxito.');
  } catch (error) {
    alert('Error al enviar el mensaje.');
  } finally {
    isLoading.value = false;
  }
};
</script>
