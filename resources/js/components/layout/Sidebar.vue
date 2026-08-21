<template>
  <aside
    class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white transform transition-transform duration-200 ease-in-out lg:translate-x-0"
    :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <!-- Brand -->
    <div class="flex items-center h-16 px-6 border-b border-slate-700">
      <span class="text-lg font-semibold tracking-tight">RoundGuard</span>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-3 space-y-1">
      <router-link
        to="/"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
        :class="isActive('/') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
      >
        <span>Dashboard</span>
      </router-link>

      <router-link
        to="/rounds"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
        :class="isActive('/rounds') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
      >
        <span>Registros de Ronda</span>
      </router-link>

      <router-link
        v-if="canManage"
        to="/checkpoints"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
        :class="isActive('/checkpoints') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
      >
        <span>Pontos de Ronda</span>
      </router-link>

      <router-link
        v-if="canManage"
        to="/users"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
        :class="isActive('/users') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
      >
        <span>Usuários</span>
      </router-link>

      <router-link
        v-if="canManage"
        to="/activity-log"
        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition"
        :class="isActive('/activity-log') ? 'bg-sky-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
      >
        <span>Log de Atividades</span>
      </router-link>
    </nav>

    <!-- Bottom -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-700">
      <p class="text-xs text-slate-500 text-center">
        RoundGuard &copy; {{ new Date().getFullYear() }}
      </p>
    </div>
  </aside>

  <!-- Overlay mobile -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-20 bg-black/50 lg:hidden"
    @click="$emit('close')"
  ></div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

defineProps<{
  isOpen: boolean;
}>();

defineEmits<{
  close: [];
}>();

const route = useRoute();
const auth = useAuthStore();

function isActive(path: string) {
  return route.path === path;
}

const userRoles = computed(() => {
  return auth.user?.roles?.map((r: any) => r.name) || [];
});

const isAdmin = computed(() => {
  return userRoles.value.includes('admin');
});

const canManage = computed(() => {
  return userRoles.value.includes('admin') || userRoles.value.includes('developer');
});

</script>