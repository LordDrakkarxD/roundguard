<template>
  <div>
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Dashboard</h1>
    <p class="text-gray-500 mb-6">Visão geral do sistema</p>

    <div v-if="loading" class="text-gray-500">Carregando estatísticas...</div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Pontos de Ronda -->
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Pontos de Ronda</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.checkpoints }}</p>
        <p class="text-xs text-gray-400 mt-2">Ativos no sistema</p>
      </div>

      <!-- Rondas hoje -->
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Rondas hoje</p>
        <p class="text-3xl font-bold text-sky-600 mt-1">{{ stats.rounds_today }}</p>
        <p class="text-xs text-gray-400 mt-2">Registros de hoje</p>
      </div>

      <!-- Agentes ativos -->
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Agentes ativos</p>
        <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.active_agents }}</p>
        <p class="text-xs text-gray-400 mt-2">Com perfil agente</p>
      </div>

      <!-- Total de usuários -->
      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Usuários ativos</p>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total_users }}</p>
        <p class="text-xs text-gray-400 mt-2">Todos os perfis</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const stats = ref({
  checkpoints: 0,
  rounds_today: 0,
  active_agents: 0,
  total_users: 0,
});

async function fetchStats() {
  try {
    const { data } = await axios.get('/api/dashboard/stats');
    stats.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

onMounted(fetchStats);
</script>
