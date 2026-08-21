<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Pontos de Ronda</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie os pontos de verificação</p>
      </div>
      <router-link
        to="/checkpoints/create"
        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
      >
        + Novo Ponto
      </router-link>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">
        Carregando...
      </div>

      <div v-else-if="checkpoints.length === 0" class="p-8 text-center text-gray-500">
        Nenhum ponto cadastrado ainda.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Código</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">QR Code</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
              <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in checkpoints" :key="item.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">
                {{ item.name }}
              </td>
              <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                {{ item.code }}
              </td>
              <td class="px-4 py-3">
                <img
                  :src="`/api/checkpoints/${item.id}/qrcode`"
                  alt="QR Code"
                  class="w-16 h-16 border border-gray-200 rounded"
                />
              </td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                >
                  {{ item.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-3">
                <a
                  :href="`/api/checkpoints/${item.id}/print`"
                  target="_blank"
                  class="text-gray-600 hover:text-gray-900 text-sm font-medium"
                >
                  Imprimir
                </a>
                <router-link
                  :to="`/checkpoints/${item.id}/edit`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Editar
                </router-link>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="confirmDelete(item)"
                >
                  Excluir
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface Checkpoint {
  id: number;
  name: string;
  code: string;
  description: string | null;
  is_active: boolean;
}

const checkpoints = ref<Checkpoint[]>([]);
const loading = ref(true);

async function fetchCheckpoints() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/checkpoints');
    checkpoints.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(item: Checkpoint) {
  if (!confirm(`Tem certeza que deseja excluir o ponto "${item.name}"?`)) return;

  try {
    await axios.delete(`/api/checkpoints/${item.id}`);
    checkpoints.value = checkpoints.value.filter(c => c.id !== item.id);
  } catch (error) {
    alert('Erro ao excluir o ponto.');
  }
}

onMounted(fetchCheckpoints);
</script>