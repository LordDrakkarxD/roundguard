<template>
  <div class="min-h-screen flex">
    <!-- Lado esquerdo (branding) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-sky-600 to-cyan-500 items-center justify-center p-12">
      <div class="text-white max-w-md">
        <h1 class="text-4xl font-bold mb-4">RoundGuard</h1>
        <p class="text-xl text-sky-100 mb-8">
          Sistema de Controle de Rondas
        </p>
        <p class="text-sky-100/80 leading-relaxed">
          Controle de pontos, registros de ronda e gestão de agentes em um só lugar.
        </p>
      </div>
    </div>

    <!-- Lado direito (formulário) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
      <div class="w-full max-w-md">
        <div class="text-center mb-8 lg:hidden">
          <h1 class="text-3xl font-bold text-gray-800">RoundGuard</h1>
          <p class="text-gray-500 mt-2">Sistema de Controle de Rondas</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
          <h2 class="text-2xl font-semibold text-gray-800 mb-1">Entrar</h2>
          <p class="text-gray-500 mb-6 text-sm">Acesse sua conta para continuar</p>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                E-mail ou Usuário
              </label>
              <input
                v-model="login"
                type="text"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition"
                placeholder="Digite seu e-mail ou usuário"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Senha
              </label>
              <input
                v-model="password"
                type="password"
                required
                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition"
                placeholder="••••••••"
              />
            </div>

            <div v-if="error" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">
              {{ error }}
            </div>

            <button
              type="submit"
              :disabled="auth.loading"
              class="w-full bg-sky-600 hover:bg-sky-700 text-white font-medium py-2.5 px-4 rounded-lg transition disabled:opacity-60 disabled:cursor-not-allowed"
            >
              {{ auth.loading ? 'Entrando...' : 'Entrar' }}
            </button>
          </form>
        </div>

        <p class="text-center text-sm text-gray-400 mt-8">
          RoundGuard &copy; {{ new Date().getFullYear() }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const login = ref('');
const password = ref('');
const error = ref('');

async function handleLogin() {
  error.value = '';
  try {
    await auth.login(login.value, password.value);
  } catch (err: any) {
    error.value =
      err.response?.data?.message ||
      err.response?.data?.errors?.login?.[0] ||
      'Erro ao fazer login. Verifique suas credenciais.';
  }
}
</script>