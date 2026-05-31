import { defineStore } from 'pinia'
import { getCurrentUser } from '../api/auth'
import api from '../api/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('auth_user') || 'null'),
    token: localStorage.getItem('auth_token'),
    initialized: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    setSession({ user, token }) {
      this.user = user
      this.token = token

      localStorage.setItem('auth_user', JSON.stringify(user))
      localStorage.setItem('auth_token', token)
    },

    async login(credentials) {
      const { data } = await api.post('/login', credentials)

      this.setSession({
        user: data.user,
        token: data.token,
      })

      return data
    },

    async register(payload) {
      const { data } = await api.post('/register', payload)

      this.setSession({
        user: data.user,
        token: data.token,
      })

      return data
    },

    async fetchCurrentUser() {
      if (!this.token) {
        this.initialized = true
        return null
      }

      try {
        const { data } = await getCurrentUser()
        this.user = data.user
        localStorage.setItem('auth_user', JSON.stringify(data.user))
        return data.user
      } catch (error) {
        this.clearSession()
        return null
      } finally {
        this.initialized = true
      }
    },

    clearSession() {
      this.user = null
      this.token = null

      localStorage.removeItem('auth_user')
      localStorage.removeItem('auth_token')
    },

    async logout() {
      try {
        if (this.token) {
          await api.post('/logout')
        }
      } finally {
        this.clearSession()
      }
    },
  },
})
