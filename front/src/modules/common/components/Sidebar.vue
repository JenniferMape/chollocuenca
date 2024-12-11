<template>
  <aside class="w-1/4 bg-base-100 shadow-md p-6 rounded-lg min-h-screen">
    <h2 class="font-bold text-xl mb-6">Ajustes</h2>
    <ul class="menu">
      <li v-if="isCompany">
        <RouterLink
          :to="{ name: 'companyProfile' }"
          :class="{ active: $route.name === 'companyProfile' }"
        >
          <i class="fa-solid fa-user-pen mr-2"></i> Perfil
        </RouterLink>
      </li>
      <li v-else>
        <RouterLink
          :to="{ name: 'clientProfile' }"
          :class="{ active: $route.name === 'clientProfile' }"
        >
          <i class="fa-solid fa-user-pen mr-2"></i> Perfil
        </RouterLink>
      </li>
      <li v-if="isCompany">
        <RouterLink
          :to="{ name: 'companyOffers' }"
          :class="{ active: $route.name === 'companyOffers' }"
        >
          <i class="fas fa-briefcase mr-2"></i> Mis Ofertas
        </RouterLink>
      </li>
      <li v-else>
        <RouterLink
          :to="{ name: 'favoritesOffers' }"
          :class="{ active: $route.name === 'favoritesOffers' }"
        >
          <i class="fas fa-heart mr-2"></i> Mis Favoritos
        </RouterLink>
      </li>
    </ul>
  </aside>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import { RouterLink } from 'vue-router';

const props = defineProps<{
  isCompany: boolean;
}>();
</script>
