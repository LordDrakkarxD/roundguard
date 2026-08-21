<template>
  <header class="sticky top-0 z-20 bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-6">
    <!-- Botão menu mobile -->
    <button
      class="lg:hidden p-2 rounded-lg hover:bg-gray-100 mr-3"
      @click="$emit('toggle-sidebar')"
    >
      <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>

    <div class="flex-1"></div>

    <!-- Usuário -->
    <div class="relative">
      <button
        class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900"
        @click="menuOpen = !menuOpen"
      >
        <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-semibold">
          {{ userInitial }}
        </div>
        <span class="hidden sm:inline">{{ auth.user?.name }}</span>
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Dropdown -->
      <div
        v-if="menuOpen"
        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50"
      >
        <div class="px-4 py-2 border-b border-gray-100">
          <p class="text-sm font-medium text-gray-800">{{ auth.user?.name }}</p>
          <p class="text-xs text-gray-500">{{ auth.user?.email }}</p>
        </div>
        <button
          class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
          @click="handleLogout"
        >
          Sair
        </button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

defineEmits<{
  'toggle-sidebar': [];
}>();

const auth = useAuthStore();
const menuOpen = ref(false);

const userInitial = computed(() => {
  return auth.user?.name?.charAt(0)?.toUpperCase() || 'U';
});

async function handleLogout() {
  menuOpen.value = false;
  await auth.logout();
}

// Fecha o menu ao clicar fora
function handleClickOutside(e: MouseEvent) {
  const target = e.target as HTMLElement;
  if (!target.closest('.relative')) {
    menuOpen.value = false;
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>