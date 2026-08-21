<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Novo Ponto de Ronda</h1>
      <p class="text-gray-500 text-sm mt-1">Cadastre um novo ponto de verificação</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition"
            placeholder="Ex: Portaria Principal"
          />
          <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition"
            placeholder="Informações adicionais (opcional)"
          ></textarea>
        </div>

        <div class="flex items-center gap-2">
          <input
            id="is_active"
            v-model="form.is_active"
            type="checkbox"
            class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500"
          />
          <label for="is_active" class="text-sm text-gray-700">Ponto ativo</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
          <button
            type="submit"
            :disabled="loading"
            class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-60"
          >
            {{ loading ? 'Salvando...' : 'Salvar' }}
          </button>
          <router-link
            to="/checkpoints"
            class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800"
          >
            Cancelar
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);
const errors = ref<Record<string, string[]>>({});

const form = reactive({
  name: '',
  description: '',
  is_active: true,
});

async function handleSubmit() {
  loading.value = true;
  errors.value = {};

  try {
    await axios.post('/api/checkpoints', form);
    router.push('/checkpoints');
  } catch (err: any) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
    } else {
      alert('Erro ao salvar o ponto.');
    }
  } finally {
    loading.value = false;
  }
}
</script>