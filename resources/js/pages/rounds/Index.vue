<template>
    <div>
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6"
        >
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Registros de Ronda
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Histórico de movimentações
                </p>
            </div>
            <router-link
                to="/rounds/scan"
                class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-lg transition"
            >
                + Novo Registro
            </router-link>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div v-if="!isAgent">
                    <label class="block text-xs font-medium text-gray-500 mb-1"
                        >Agente</label
                    >
                    <select
                        v-model="filters.user_id"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                        @change="fetchLogs"
                    >
                        <option value="">Todos</option>
                        <option
                            v-for="user in users"
                            :key="user.id"
                            :value="user.id"
                        >
                            {{ user.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1"
                        >Ponto</label
                    >
                    <select
                        v-model="filters.checkpoint_id"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                        @change="fetchLogs"
                    >
                        <option value="">Todos</option>
                        <option
                            v-for="cp in checkpoints"
                            :key="cp.id"
                            :value="cp.id"
                        >
                            {{ cp.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1"
                        >Mês</label
                    >
                    <select
                        v-model="filters.month"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                        @change="fetchLogs"
                    >
                        <option value="">Todos</option>
                        <option
                            v-for="(name, num) in months"
                            :key="num"
                            :value="num"
                        >
                            {{ name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1"
                        >Ano</label
                    >
                    <select
                        v-model="filters.year"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none"
                        @change="fetchLogs"
                    >
                        <option value="">Todos</option>
                        <option v-for="y in years" :key="y" :value="y">
                            {{ y }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div v-if="loading" class="p-8 text-center text-gray-500">
                Carregando...
            </div>

            <div
                v-else-if="logs.length === 0"
                class="p-8 text-center text-gray-500"
            >
                Nenhum registro encontrado.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="text-left px-4 py-3 font-medium text-gray-600"
                            >
                                Agente
                            </th>
                            <th
                                class="text-left px-4 py-3 font-medium text-gray-600"
                            >
                                Ponto
                            </th>
                            <th
                                class="text-left px-4 py-3 font-medium text-gray-600"
                            >
                                Localização
                            </th>
                            <th
                                class="text-left px-4 py-3 font-medium text-gray-600"
                            >
                                Data / Hora
                            </th>
                            <th class="text-right px-4 py-3 font-medium text-gray-600">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr
                            v-for="log in logs"
                            :key="log.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ log.user?.name || "—" }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ log.checkpoint?.name || "—" }}
                            </td>
                            <td class="px-4 py-3">
                                <template v-if="log.latitude && log.longitude">
                                    <a
                                        :href="`https://www.google.com/maps?q=${log.latitude},${log.longitude}`"
                                        target="_blank"
                                        rel="noopener"
                                        class="text-sky-600 hover:text-sky-800 text-sm"
                                    >
                                        Ver no mapa
                                    </a>
                                </template>
                                <span v-else class="text-gray-400 text-sm"
                                    >—</span
                                >
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ formatDate(log.scanned_at) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button
                                    v-if="canDelete"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                    @click="confirmDelete(log)"
                                >
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação simples -->
            <div
                v-if="pagination.last_page > 1"
                class="px-4 py-3 border-t border-gray-100 flex justify-between items-center text-sm"
            >
                <span class="text-gray-500">
                    Página {{ pagination.current_page }} de
                    {{ pagination.last_page }}
                </span>
                <div class="flex gap-2">
                    <button
                        :disabled="pagination.current_page === 1"
                        class="px-3 py-1 rounded border border-gray-300 disabled:opacity-40 hover:bg-gray-50"
                        @click="changePage(pagination.current_page - 1)"
                    >
                        Anterior
                    </button>
                    <button
                        :disabled="
                            pagination.current_page === pagination.last_page
                        "
                        class="px-3 py-1 rounded border border-gray-300 disabled:opacity-40 hover:bg-gray-50"
                        @click="changePage(pagination.current_page + 1)"
                    >
                        Próxima
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { computed } from "vue";
import axios from "axios";

const logs = ref<any[]>([]);
const users = ref<any[]>([]);
const checkpoints = ref<any[]>([]);
const loading = ref(true);

const auth = useAuthStore();

const pagination = reactive({
    current_page: 1,
    last_page: 1,
});

const filters = reactive({
    user_id: "",
    checkpoint_id: "",
    month: "",
    year: "",
});

const months: Record<string, string> = {
    "01": "Janeiro",
    "02": "Fevereiro",
    "03": "Março",
    "04": "Abril",
    "05": "Maio",
    "06": "Junho",
    "07": "Julho",
    "08": "Agosto",
    "09": "Setembro",
    "10": "Outubro",
    "11": "Novembro",
    "12": "Dezembro",
};

const isAgent = computed(() => {
    return auth.user?.roles?.some((r: any) => r.name === "vigilante") ?? false;
});

const canDelete = computed(() => {
  const roles = auth.user?.roles?.map((r: any) => r.name) || [];
  return roles.includes('admin') || roles.includes('developer');
});

async function confirmDelete(log: any) {
  if (!confirm('Tem certeza que deseja excluir este registro?')) return;

  try {
    await axios.delete(`/api/rounds/${log.id}`);
    logs.value = logs.value.filter(l => l.id !== log.id);
  } catch (err: any) {
    alert(err.response?.data?.message || 'Erro ao excluir o registro.');
  }
}

const currentYear = new Date().getFullYear();
const years = [currentYear, currentYear - 1, currentYear - 2];

function formatDate(dateStr: string) {
    if (!dateStr) return "—";
    const d = new Date(dateStr);
    return d.toLocaleString("pt-BR");
}

async function fetchLogs(page = 1) {
    loading.value = true;
    try {
        const params: any = { page };
        if (filters.user_id) params.user_id = filters.user_id;
        if (filters.checkpoint_id) params.checkpoint_id = filters.checkpoint_id;
        if (filters.month) params.month = filters.month;
        if (filters.year) params.year = filters.year;

        const { data } = await axios.get("/api/rounds", { params });
        logs.value = data.data;
        pagination.current_page = data.current_page;
        pagination.last_page = data.last_page;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
}

async function fetchFiltersData() {
    try {
        const [usersRes, checkpointsRes] = await Promise.all([
            axios.get("/api/user"), // temporário - depois criamos endpoint de users
            axios.get("/api/checkpoints"),
        ]);
        // Por enquanto só temos o usuário logado. Depois melhoramos.
        checkpoints.value = checkpointsRes.data;
    } catch (error) {
        console.error(error);
    }
}

function changePage(page: number) {
    fetchLogs(page);
}

onMounted(async () => {
    await fetchFiltersData();
    await fetchLogs();
});
</script>
