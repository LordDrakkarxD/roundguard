<template>
  <div class="min-h-screen bg-gray-50">
    <Sidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Conteúdo principal -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
      <Topbar @toggle-sidebar="sidebarOpen = !sidebarOpen" />

      <main class="flex-1 p-4 lg:p-6">
        <router-view />
      </main>

      <Footer />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Sidebar from '@/components/layout/Sidebar.vue';
import Topbar from '@/components/layout/Topbar.vue';
import Footer from '@/components/layout/Footer.vue';

const sidebarOpen = ref(false);
</script>