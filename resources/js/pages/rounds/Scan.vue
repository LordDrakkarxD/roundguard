<template>
  <div class="max-w-lg mx-auto">
    <div class="mb-6 text-center">
      <h1 class="text-2xl font-bold text-gray-800">Registrar Ponto</h1>
      <p class="text-gray-500 text-sm mt-1">Aponte a câmera para o QR Code do local</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
      <!-- Status -->
      <div class="flex flex-wrap gap-2 mb-4">
        <span
          class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
          :class="cameraStatusClass"
        >
          📷 {{ cameraStatus }}
        </span>
        <span
          class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
          :class="geoStatusClass"
        >
          📍 {{ geoStatus }}
        </span>
      </div>

      <!-- Scanner -->
      <div class="rounded-xl overflow-hidden border-2 border-dashed border-gray-300 bg-gray-50 mb-4">
        <div id="reader" class="w-full"></div>
      </div>

      <!-- OU selecionar imagem -->
      <div class="text-center">
        <p class="text-sm text-gray-500 mb-2">ou</p>
        <label
          class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer transition"
        >
          <span>📁 Selecionar imagem do QR Code</span>
          <input
            type="file"
            accept="image/*"
            class="hidden"
            @change="onFileSelected"
          />
        </label>
      </div>

      <!-- Mensagem -->
      <div v-if="message" class="mt-4 p-3 rounded-lg text-sm" :class="messageClass">
        {{ message }}
      </div>

      <!-- Ações -->
      <div class="mt-5 flex gap-3">
        <router-link
          to="/rounds"
          class="flex-1 text-center px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50"
        >
          Voltar
        </router-link>
        <button
          v-if="showRetry"
          class="flex-1 px-4 py-2.5 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700"
          @click="restartScanner"
        >
          Tentar novamente
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { Html5Qrcode } from 'html5-qrcode';

const router = useRouter();

const cameraStatus = ref('Aguardando');
const geoStatus = ref('Aguardando');
const message = ref('');
const messageType = ref<'success' | 'error' | 'warning'>('success');
const showRetry = ref(false);

let scanner: Html5Qrcode | null = null;
let alreadyScanned = false;
let latitude: number | null = null;
let longitude: number | null = null;

const cameraStatusClass = computed(() => {
  if (cameraStatus.value.includes('OK')) return 'bg-green-100 text-green-700';
  if (cameraStatus.value.includes('Erro')) return 'bg-red-100 text-red-700';
  return 'bg-gray-100 text-gray-600';
});

const geoStatusClass = computed(() => {
  if (geoStatus.value.includes('OK')) return 'bg-green-100 text-green-700';
  if (geoStatus.value.includes('negada') || geoStatus.value.includes('indisponível')) return 'bg-red-100 text-red-700';
  return 'bg-gray-100 text-gray-600';
});

const messageClass = computed(() => {
  if (messageType.value === 'success') return 'bg-green-50 text-green-700';
  if (messageType.value === 'error') return 'bg-red-50 text-red-700';
  return 'bg-yellow-50 text-yellow-700';
});

function getLocation() {
  if (!navigator.geolocation) {
    geoStatus.value = 'indisponível';
    return;
  }

  geoStatus.value = 'Pedindo permissão…';

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      latitude = pos.coords.latitude;
      longitude = pos.coords.longitude;
      geoStatus.value = 'OK';
    },
    () => {
      geoStatus.value = 'negada';
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

async function startScanner() {
  alreadyScanned = false;
  showRetry.value = false;
  message.value = '';
  cameraStatus.value = 'Iniciando…';

  try {
    scanner = new Html5Qrcode('reader');

    await scanner.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: { width: 250, height: 250 } },
      onScanSuccess,
      () => {}
    );

    cameraStatus.value = 'OK';
  } catch (err) {
    console.error(err);
    cameraStatus.value = 'Erro';
    message.value = 'Não foi possível abrir a câmera. Verifique as permissões.';
    messageType.value = 'error';
    showRetry.value = true;
  }
}

async function stopScanner() {
  if (scanner) {
    try {
      if (scanner.isScanning) {
        await scanner.stop();
      }
    } catch (e) {
      // ignore
    }
  }
}

async function onFileSelected(event: Event) {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (!file) return;

  message.value = 'Lendo imagem...';
  messageType.value = 'warning';

  try {
    // Para de usar a câmera se estiver ativa
    await stopScanner();

    if (!scanner) {
      scanner = new Html5Qrcode('reader');
    }

    const decodedText = await scanner.scanFile(file, true);
    
    // Sucesso
    onScanSuccess(decodedText);
  } catch (err) {
    console.error(err);
    message.value = 'Não foi possível ler o QR Code da imagem. Tente outra.';
    messageType.value = 'error';
    showRetry.value = true;
  }
}

function onScanSuccess(decodedText: string) {
  if (alreadyScanned) return;
  alreadyScanned = true;

  message.value = 'QR Code reconhecido! Redirecionando…';
  messageType.value = 'success';

  stopScanner();

  // Guarda temporariamente os dados e vai para confirmação
  sessionStorage.setItem('pending_round', JSON.stringify({
    code: decodedText,
    latitude,
    longitude,
  }));

  router.push('/rounds/confirm');
}

function restartScanner() {
  stopScanner().then(() => {
    startScanner();
    getLocation();
  });
}

onMounted(() => {
  getLocation();
  startScanner();
});

onUnmounted(() => {
  stopScanner();
});
</script>