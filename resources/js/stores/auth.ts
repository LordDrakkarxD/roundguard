import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import router from '@/router';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<any>(null);
  const loading = ref(false);

  const isAuthenticated = computed(() => !!user.value);

  async function fetchUser() {
    try {
      const { data } = await axios.get('/api/user');
      user.value = data;
    } catch (error) {
      user.value = null;
    }
  }

  async function login(login: string, password: string) {
    loading.value = true;
    try {
      // Pega o cookie CSRF primeiro
      await axios.get('/sanctum/csrf-cookie');

      const { data } = await axios.post('/login', {
        login,
        password,
      });

      user.value = data.user;
      await router.push('/');
    } catch (error: any) {
      throw error;
    } finally {
      loading.value = false;
    }
  }

  async function logout() {
    try {
      await axios.post('/logout');
      user.value = null;
      await router.push('/login');
    } catch (error) {
      console.error(error);
    }
  }

  return {
    user,
    loading,
    isAuthenticated,
    fetchUser,
    login,
    logout,
  };
});