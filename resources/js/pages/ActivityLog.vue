<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Log de Atividades</h1>
      <p class="text-gray-500 text-sm mt-1">Últimas ações registradas no sistema</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="activities.length === 0" class="p-8 text-center text-gray-500">
        Nenhuma atividade registrada ainda.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Data</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Usuário</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Ação</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Tipo</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in activities" :key="item.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-gray-600">{{ item.created_at }}</td>
              <td class="px-4 py-3 font-medium text-gray-800">{{ item.causer || 'Sistema' }}</td>
              <td class="px-4 py-3 text-gray-700">{{ item.description }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                  {{ item.subject_type || '—' }}
                </span>
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

const activities = ref<any[]>([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/activity-log');
    activities.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
});
</script>