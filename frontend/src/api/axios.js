import axios from 'axios'

const normalizeUrl = (value) => value?.trim().replace(/\/+$/, '') || ''

const resolveApiBaseUrl = () => {
  const configuredApiUrl = normalizeUrl(import.meta.env.VITE_API_URL)

  if (configuredApiUrl) {
    return configuredApiUrl
  }

  const { protocol, hostname } = window.location

  if (hostname === 'localhost' || hostname === '127.0.0.1') {
    return `${protocol}//127.0.0.1:8000/api`
  }

  return '/api'
}

const api = axios.create({
  baseURL: resolveApiBaseUrl(),
  headers: {
    Accept: 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  return config
})

export default api
