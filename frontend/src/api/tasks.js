import api from './axios'

export const getProjectTasks = (projectId) => api.get(`/projects/${projectId}/tasks`)

export const getTask = (id) => api.get(`/tasks/${id}`)

export const createTask = (projectId, payload) =>
  api.post(`/projects/${projectId}/tasks`, payload)

export const updateTask = (id, payload) => api.patch(`/tasks/${id}`, payload)

export const deleteTask = (id) => api.delete(`/tasks/${id}`)
