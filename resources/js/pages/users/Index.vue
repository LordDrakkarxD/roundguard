<template>
  <div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Usuários</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie as contas do sistema</p>
      </div>
      <router-link
        to="/users/create"
        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
      >
        + Novo Usuário
      </router-link>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div v-if="loading" class="p-8 text-center text-gray-500">Carregando...</div>

      <div v-else-if="users.length === 0" class="p-8 text-center text-gray-500">
        Nenhum usuário cadastrado.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Nome</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Usuário</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">E-mail</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Perfil</th>
              <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
              <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 font-medium text-gray-800">{{ user.name }}</td>
              <td class="px-4 py-3 text-gray-600">{{ user.username }}</td>
              <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">
                  {{ user.roles?.[0]?.name || '—' }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                >
                  {{ user.is_active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right space-x-3">
                <router-link
                  :to="`/users/${user.id}/edit`"
                  class="text-sky-600 hover:text-sky-800 text-sm font-medium"
                >
                  Editar
                </router-link>
                <button
                  class="text-red-600 hover:text-red-800 text-sm font-medium"
                  @click="confirmDelete(user)"
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

const users = ref<any[]>([]);
const loading = ref(true);

async function fetchUsers() {
  loading.value = true;
  try {
    const { data } = await axios.get('/api/users');
    users.value = data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

async function confirmDelete(user: any) {
  if (!confirm(`Tem certeza que deseja excluir o usuário "${user.name}"?`)) return;

  try {
    await axios.delete(`/api/users/${user.id}`);
    users.value = users.value.filter(u => u.id !== user.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir o usuário.');
  }
}

onMounted(fetchUsers);
</script>