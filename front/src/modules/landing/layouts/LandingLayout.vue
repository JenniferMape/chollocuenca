<template>
  <div class="flex flex-col min-h-screen bg-base-200">
    <router-view v-slot="{ Component }">
      <keep-alive>
        <component :is="Component" />
      </keep-alive>
    </router-view>
    <footer class="mt-auto">
      <CustomFooter />
    </footer>
  </div>
</template>

<script setup lang="ts">
import CustomFooter from '@/modules/common/components/CustomFooter.vue';
</script>
