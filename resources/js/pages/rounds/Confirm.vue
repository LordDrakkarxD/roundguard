<template>
  <div class="max-w-lg mx-auto">
    <div class="mb-6 text-center">
      <h1 class="text-2xl font-bold text-gray-800">Confirmar Registro</h1>
      <p class="text-gray-500 text-sm mt-1">Verifique os dados antes de confirmar</p>
    </div>

    <div v-if="loading" class="text-center text-gray-500 py-12">
      Carregando...
    </div>

    <div v-else-if="error" class="bg-red-50 text-red-700 p-4 rounded-xl text-center">
      {{ error }}
      <div class="mt-4">
        <router-link to="/rounds/scan" class="text-sky-600 font-medium">
          Voltar para escanear
        </router-link>
      </div>
    </div>

    <div v-else class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
      <div class="space-y-4">
        <div class="p-4 rounded-xl bg-gray-50">
          <p class="text-xs text-gray-500 mb-1">Funcionário</p>
          <p class="font-semibold text-gray-800">{{ auth.user?.name }}</p>
        </div>

        <div class="p-4 rounded-xl bg-gray-50">
          <p class="text-xs text-gray-500 mb-1">Ponto / QR</p>
          <p class="font-semibold text-gray-800">{{ checkpointName || pending.code }}</p>
        </div>

        <div class="p-4 rounded-xl bg-gray-50">
          <p class="text-xs text-gray-500 mb-1">Localização</p>
          <p class="font-semibold text-gray-800">
            <template v-if="pending.latitude && pending.longitude">
              {{ pending.latitude.toFixed(6) }}, {{ pending.longitude.toFixed(6) }}
              <a
                :href="`https://www.google.com/maps?q=${pending.latitude},${pending.longitude}`"
                target="_blank"
                class="ml-2 text-sky-600 text-sm font-medium"
              >
                Ver mapa
              </a>
            </template>
            <span v-else class="text-gray-400">Não informada</span>
          </p>
        </div>

        <div class="p-4 rounded-xl bg-gray-50">
          <p class="text-xs text-gray-500 mb-1">Horário</p>
          <p class="font-semibold text-gray-800 text-lg">
            {{ currentTime }}
          </p>
        </div>
      </div>

      <div class="mt-6 p-3 rounded-lg bg-amber-50 text-amber-800 text-sm">
        Ao confirmar, o registro será salvo com data, hora e localização.
      </div>

      <div class="mt-6 space-y-3">
        <button
          :disabled="saving"
          class="w-full py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-semibold transition disabled:opacity-60"
          @click="confirmRegister"
        >
          {{ saving ? 'Salvando...' : '✅ Confirmar registro' }}
        </button>

        <router-link
          to="/rounds/scan"
          class="block w-full text-center py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50"
        >
          ↩ Voltar para escanear
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();

const pending = ref<any>(null);
const checkpointName = ref('');
const loading = ref(true);
const error = ref('');
const saving = ref(false);
const currentTime = ref('');

onMounted(async () => {
  const raw = sessionStorage.getItem('pending_round');
  if (!raw) {
    error.value = 'Nenhum QR Code pendente. Escaneie novamente.';
    loading.value = false;
    return;
  }

  pending.value = JSON.parse(raw);
  currentTime.value = new Date().toLocaleString('pt-BR');

  // Tenta buscar o nome do ponto pelo código
  try {
    const { data } = await axios.get('/api/checkpoints');
    const found = data.find((c: any) => c.code === pending.value.code);
    if (found) {
      checkpointName.value = found.name;
    }
  } catch (e) {
    // ignora
  }

  loading.value = false;
});

async function confirmRegister() {
  saving.value = true;
  try {
    await axios.post('/api/rounds', {
      code: pending.value.code,
      latitude: pending.value.latitude,
      longitude: pending.value.longitude,
    });

    sessionStorage.removeItem('pending_round');
    router.push('/rounds');
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Erro ao salvar o registro.';
  } finally {
    saving.value = false;
  }
}
</script>