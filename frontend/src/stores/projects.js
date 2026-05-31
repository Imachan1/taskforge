import { defineStore } from 'pinia'
import {
  createProject,
  deleteProject,
  getProjects,
  updateProject,
} from '../api/projects'

export const useProjectsStore = defineStore('projects', {
  state: () => ({
    projects: [],
    loading: false,
    saving: false,
    error: '',
  }),

  actions: {
    async fetchProjects() {
      this.loading = true
      this.error = ''

      try {
        const { data } = await getProjects()
        this.projects = data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to load projects.'
      } finally {
        this.loading = false
      }
    },

    async create(payload) {
      this.saving = true
      this.error = ''

      try {
        const { data } = await createProject(payload)
        this.projects = [data.data, ...this.projects]
        return data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to create project.'
        throw error
      } finally {
        this.saving = false
      }
    },

    async update(id, payload) {
      this.saving = true
      this.error = ''

      try {
        const { data } = await updateProject(id, payload)
        this.projects = this.projects.map((project) =>
          project.id === id ? data.data : project,
        )
        return data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to update project.'
        throw error
      } finally {
        this.saving = false
      }
    },

    async remove(id) {
      this.error = ''

      try {
        await deleteProject(id)
        this.projects = this.projects.filter((project) => project.id !== id)
      } catch (error) {
        this.error = error.response?.data?.message || 'Unable to delete project.'
        throw error
      }
    },
  },
})
