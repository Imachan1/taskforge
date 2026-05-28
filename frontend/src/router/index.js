import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProjectsView from '../views/ProjectsView.vue'
import ProjectView from '../views/ProjectView.vue'
import TasksView from '../views/TasksView.vue'
import NewsView from '../views/NewsView.vue'
import SearchView from '../views/SearchView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      redirect: '/dashboard',
    },

    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },

    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
    },

    {
      path: '/projects',
      name: 'projects',
      component: ProjectsView,
    },

    {
      path: '/projects/:id',
      name: 'project',
      component: ProjectView,
    },

    {
      path: '/tasks',
      name: 'tasks',
      component: TasksView,
    },

    {
      path: '/news',
      name: 'news',
      component: NewsView,
    },

    {
      path: '/search',
      name: 'search',
      component: SearchView,
    },
  ],
})

export default router