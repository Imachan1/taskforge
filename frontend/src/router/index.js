import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProjectsView from '../views/ProjectsView.vue'
import ProjectView from '../views/ProjectView.vue'
import NewsView from '../views/NewsView.vue'
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
          path: 'news',
          name: 'news',
          component: NewsView,
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

router.beforeEach((to) => {
  const auth = useAuthStore()

  if (!auth.isAuthenticated && to.name !== 'login') {
    return { name: 'login' }
  }
})

export default router
