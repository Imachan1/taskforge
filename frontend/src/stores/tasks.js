import { defineStore } from 'pinia'
import {
  createTask,
  deleteTask,
  getProjectTasks,
  updateTask,
} from '../api/tasks'

export const useTasksStore = defineStore('tasks', {
  state: () => ({
    tasks: [],
    loading: false,
    saving: false,
    error: '',
  }),

  actions: {
    async fetchProjectTasks(projectId) {
      this.loading = true
      this.error = ''

      try {
        const { data } = await getProjectTasks(projectId)
        this.tasks = data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to load tasks.'
        throw error
      } finally {
        this.loading = false
      }
    },

    async create(projectId, payload) {
      this.saving = true
      this.error = ''

      try {
        const { data } = await createTask(projectId, payload)
        this.tasks = [data.data, ...this.tasks]
        return data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to create task.'
        throw error
      } finally {
        this.saving = false
      }
    },

    async update(id, payload) {
      this.saving = true
      this.error = ''

      try {
        const { data } = await updateTask(id, payload)
        this.tasks = this.tasks.map((task) => (task.id === id ? data.data : task))
        return data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to update task.'
        throw error
      } finally {
        this.saving = false
      }
    },

    async remove(id) {
      this.error = ''

      try {
        await deleteTask(id)
        this.tasks = this.tasks.filter((task) => task.id !== id)
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to delete task.'
        throw error
      }
    },

    clear() {
      this.tasks = []
      this.error = ''
    },
  },
})
