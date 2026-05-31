import api from './axios'

export const getProfile = () => api.get('/profile')

export const updateProfile = (payload) => api.put('/profile', payload)

export const updatePassword = (payload) => api.put('/profile/password', payload)
