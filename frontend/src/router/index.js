import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import GoogleCallbackView from '../views/GoogleCallbackView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProjectsView from '../views/ProjectsView.vue'
import ProjectView from '../views/ProjectView.vue'
import SearchView from '../views/SearchView.vue'

import MainLayout from '../layouts/MainLayout.vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      redirect: '/dashboard',
    },

    {
      path: '/',
      component: AuthLayout,

      children: [
        {
          path: 'login',
          name: 'login',
          component: LoginView,
        },
        {
          path: 'register',
          name: 'register',
          component: RegisterView,
        },
        {
          path: 'auth/google/callback',
          name: 'google-callback',
          component: GoogleCallbackView,
        },
      ],
    },

    {
      path: '/',
      component: MainLayout,

      children: [
        {
          path: 'dashboard',
          name: 'dashboard',
          component: DashboardView,
        },

        {
          path: 'projects',
          name: 'projects',
          component: ProjectsView,
        },

        {
          path: 'projects/:id',
          name: 'project',
          component: ProjectView,
        },

        {
          path: 'search',
          name: 'search',
          component: SearchView,
        },
      ],
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  const publicRoutes = ['login', 'register', 'google-callback']

  if (to.name === 'google-callback') {
    return true
  }

  if (auth.isAuthenticated && !auth.initialized) {
    await auth.fetchCurrentUser()
  }

  if (!auth.isAuthenticated && !publicRoutes.includes(to.name)) {
    return { name: 'login' }
  }

  if (auth.isAuthenticated && ['login', 'register'].includes(to.name)) {
    return { name: 'dashboard' }
  }
})

export default router
