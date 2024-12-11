<template>
  <div class="flex flex-col min-h-screen bg-base-200">
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

    <!-- Contenedor principal de contenido -->
    <main class="flex-grow">
      <router-view v-slot="{ Component }">
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </main>

    <!-- Footer siempre al fondo -->
    <footer class="mt-auto">
      <CustomFooter />
    </footer>
  </div>
</template>

<script setup lang="ts">
import NavBar from '@/modules/common/components/NavBar/NavBar.vue';
import LogoNav from '@/modules/common/components/NavBar/components/LogoNav.vue';
import AuthButtons from '@/modules/common/components/NavBar/components/AuthButtons/AuthButtons.vue';
import ThemeToggle from '@/modules/common/components/NavBar/components/ThemeToggle.vue';
import CustomFooter from '@/modules/common/components/CustomFooter.vue';
import NavItems from '@/modules/common/components/NavItems.vue';
</script>
