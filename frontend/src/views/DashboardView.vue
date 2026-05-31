<script setup>
import Card from 'primevue/card'
import Message from 'primevue/message'
import ProgressSpinner from 'primevue/progressspinner'
import Tag from 'primevue/tag'

import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { getDashboard } from '../api/dashboard'

const router = useRouter()

const loading = ref(false)
const errorMessage = ref('')
const dashboard = ref(null)

const statCards = ref([])

const statusLabels = {
  todo: 'Todo',
  in_progress: 'In Progress',
  done: 'Done',
}

const statusSeverity = {
  todo: 'secondary',
  in_progress: 'info',
  done: 'success',
}

const prioritySeverity = {
  low: 'success',
  medium: 'warn',
  high: 'danger',
}

const loadDashboard = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await getDashboard()
    dashboard.value = data
    statCards.value = [
      { label: 'Projects', value: data.projects_count },
      { label: 'Tasks', value: data.tasks_count },
      { label: 'Todo', value: data.todo_count },
      { label: 'In Progress', value: data.in_progress_count },
      { label: 'Done', value: data.done_count },
      { label: 'Completion %', value: `${data.completion_rate}%` },
    ]
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || 'Unable to load dashboard.'
  } finally {
    loading.value = false
  }
}

onMounted(loadDashboard)
</script>

<template>
  <section class="dashboard-page">
    <header>
      <h1>Dashboard</h1>
      <p>Current workload and recent activity.</p>
    </header>

    <Message
      v-if="errorMessage"
      severity="error"
      size="small"
    >
      {{ errorMessage }}
    </Message>

    <div
      v-if="loading"
      class="loading-state"
    >
      <ProgressSpinner />
    </div>

    <template v-else-if="dashboard">
      <div class="stats-grid">
        <Card
          v-for="stat in statCards"
          :key="stat.label"
        >
          <template #content>
            <div class="stat-card">
              <span>{{ stat.label }}</span>
              <strong>{{ stat.value }}</strong>
            </div>
          </template>
        </Card>
      </div>

      <div class="recent-grid">
        <Card>
          <template #title>
            Recent Projects
          </template>
          <template #content>
            <div
              v-if="dashboard.recent_projects.length === 0"
              class="empty-state"
            >
              No recent projects.
            </div>
            <button
              v-for="project in dashboard.recent_projects"
              :key="project.id"
              class="list-row"
              type="button"
              @click="router.push({ name: 'project', params: { id: project.id } })"
            >
              <strong>{{ project.name }}</strong>
              <span>{{ project.description || 'No description' }}</span>
            </button>
          </template>
        </Card>

        <Card>
          <template #title>
            Recent Tasks
          </template>
          <template #content>
            <div
              v-if="dashboard.recent_tasks.length === 0"
              class="empty-state"
            >
              No recent tasks.
            </div>
            <button
              v-for="task in dashboard.recent_tasks"
              :key="task.id"
              class="list-row"
              type="button"
              @click="router.push({ name: 'project', params: { id: task.project_id } })"
            >
              <strong>{{ task.title }}</strong>
              <span>{{ task.project_name }}</span>
              <div class="tag-row">
                <Tag
                  :value="statusLabels[task.status]"
                  :severity="statusSeverity[task.status]"
                />
                <Tag
                  :value="task.priority"
                  :severity="prioritySeverity[task.priority]"
                />
              </div>
            </button>
          </template>
        </Card>
      </div>
    </template>
  </section>
</template>

<style scoped>
.dashboard-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.dashboard-page h1 {
  margin: 0;
}

.dashboard-page p {
  margin: 0.25rem 0 0;
  color: #9dbbda;
}

.loading-state {
  display: flex;
  justify-content: center;
  padding: 3rem;
}

.stats-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.stat-card {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.stat-card span {
  color: #8fb0d2;
  font-weight: 600;
}

.stat-card strong {
  font-size: 2rem;
}

.recent-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.list-row {
  width: 100%;
  border: 0;
  border-bottom: 1px solid rgba(125, 211, 252, 0.16);
  background: transparent;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem 0;
  text-align: left;
}

.list-row:last-child {
  border-bottom: 0;
}

.list-row span {
  color: #8fb0d2;
}

.tag-row {
  display: flex;
  gap: 0.5rem;
}

.empty-state {
  color: #8aa8c9;
  padding: 1rem 0;
}

@media (max-width: 900px) {
  .stats-grid,
  .recent-grid {
    grid-template-columns: 1fr;
  }
}
</style>
