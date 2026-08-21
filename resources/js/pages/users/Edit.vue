<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Editar Usuário</h1>
      <p class="text-gray-500 text-sm mt-1">Atualize os dados da conta</p>
    </div>

    <div v-if="loadingData" class="text-gray-500">Carregando...</div>

    <div v-else class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
          <input v-model="form.name" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuário *</label>
          <input v-model="form.username" type="text" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">E-mail *</label>
          <input v-model="form.email" type="email" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nova senha</label>
          <input v-model="form.password" type="password"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
            placeholder="Deixe em branco para manter a atual" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Perfil *</label>
          <select v-model="form.role" required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none">
            <option v-for="role in roles" :key="role.id" :value="role.name">
              {{ role.name }}
            </option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <input id="is_active" v-model="form.is_active" type="checkbox"
            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500" />
          <label for="is_active" class="text-sm text-gray-700">Usuário ativo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
          <router-link to="/users" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800">
            Cancelar
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const loadingData = ref(true);
const roles = ref<any[]>([]);

const form = reactive({
  name: '',
  username: '',
  email: '',
  password: '',
  role: '',
  is_active: true,
});

async function fetchData() {
  try {
    const [userRes, rolesRes] = await Promise.all([
      axios.get(`/api/users/${route.params.id}`),
      axios.get('/api/roles'),
    ]);

    const user = userRes.data;
    form.name = user.name;
    form.username = user.username;
    form.email = user.email;
    form.is_active = user.is_active;
    form.role = user.roles?.[0]?.name || '';
    roles.value = rolesRes.data;
  } catch (error) {
    alert('Usuário não encontrado.');
    router.push('/users');
  } finally {
    loadingData.value = false;
  }
}

async function handleSubmit() {
  loading.value = true;
  try {
    const payload: any = {
      name: form.name,
      username: form.username,
      email: form.email,
      role: form.role,
      is_active: form.is_active,
    };
    if (form.password) {
      payload.password = form.password;
    }

    await axios.put(`/api/users/${route.params.id}`, payload);
    router.push('/users');
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao salvar.');
  } finally {
    loading.value = false;
  }
}

onMounted(fetchData);
</script>