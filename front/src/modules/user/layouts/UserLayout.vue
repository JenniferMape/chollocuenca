<template>
  <div class="flex flex-col min-h-screen bg-base-200">
    <header class="shadow bg-highlight">
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
    </header>

    <div class="container mx-auto p-4 flex">
      <Sidebar :isCompany="isCompanyUser" />
      <main class="flex-1">
        <router-view v-slot="{ Component }">
          <keep-alive>
            <component :is="Component" />
          </keep-alive>
        </router-view>
      </main>
    </div>
    <footer class="mt-auto">
      <CustomFooter />
    </footer>
  </div>
</template>

<script setup lang="ts">
import NavBar from '@/modules/common/components/NavBar/NavBar.vue';
import LogoNav from '@/modules/common/components/NavBar/components/LogoNav.vue';
import ThemeToggle from '@/modules/common/components/NavBar/components/ThemeToggle.vue';
import CustomFooter from '@/modules/common/components/CustomFooter.vue';
import AuthButtons from '@/modules/common/components/NavBar/components/AuthButtons/AuthButtons.vue';
import Sidebar from '@/modules/common/components/Sidebar.vue';
import { useAuthStore } from '@/modules/auth/stores/auth.store';

const authStore = useAuthStore();
const isCompanyUser = authStore.isCompany;
</script>
