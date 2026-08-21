import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import AppLayout from '@/layouts/AppLayout.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/Login.vue'),
      meta: { guest: true },
    },
    {
      path: '/',
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/pages/Home.vue'),
        },
        {
          path: 'checkpoints',
          name: 'checkpoints',
          component: () => import('@/pages/checkpoints/Index.vue'),
        },
        {
          path: 'checkpoints/create',
          name: 'checkpoints.create',
          component: () => import('@/pages/checkpoints/Create.vue'),
        },
        {
          path: 'checkpoints/:id/edit',
          name: 'checkpoints.edit',
          component: () => import('@/pages/checkpoints/Edit.vue'),
        },
        {
          path: 'rounds',
          name: 'rounds',
          component: () => import('@/pages/rounds/Index.vue'),
        },
        {
          path: 'rounds/scan',
          name: 'rounds.scan',
          component: () => import('@/pages/rounds/Scan.vue'),
        },
        {
          path: 'rounds/confirm',
          name: 'rounds.confirm',
          component: () => import('@/pages/rounds/Confirm.vue'),
        },
        {
          path: 'users',
          name: 'users',
          component: () => import('@/pages/users/Index.vue'),
        },
        {
          path: 'users/create',
          name: 'users.create',
          component: () => import('@/pages/users/Create.vue'),
        },
        {
          path: 'users/:id/edit',
          name: 'users.edit',
          component: () => import('@/pages/users/Edit.vue'),
        },
        {
          path: 'activity-log',
          name: 'activity-log',
          component: () => import('@/pages/ActivityLog.vue'),
        },
        // Futuras rotas (checkpoints, rounds...)
      ],
    },
  ],
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  if (!auth.user && to.meta.requiresAuth) {
    await auth.fetchUser();
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next('/login');
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return next('/');
  }

  next();
});

export default router;