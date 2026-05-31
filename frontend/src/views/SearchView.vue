<script setup>
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import ProgressSpinner from 'primevue/progressspinner'
import Tag from 'primevue/tag'

import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { searchWorkspace } from '../api/search'

const router = useRouter()

const query = ref('')
const loading = ref(false)
const errorMessage = ref('')
const results = ref({
  projects: [],
  tasks: [],
})
let searchTimer = null

const hasSearched = computed(() => query.value.trim().length > 0)
const hasResults = computed(
  () => results.value.projects.length > 0 || results.value.tasks.length > 0,
)

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

const runSearch = async () => {
  const currentQuery = query.value.trim()

  if (!currentQuery) {
    results.value = { projects: [], tasks: [] }
    errorMessage.value = ''
    return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await searchWorkspace(currentQuery)
    results.value = data
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Unable to search.'
  } finally {
    loading.value = false
  }
}

watch(query, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(runSearch, 300)
})
</script>

<template>
  <section class="search-page">
    <header>
      <h1>Search</h1>
      <p>Find projects and tasks across your workspace.</p>
    </header>

    <span class="search-box">
      <i class="pi pi-search" />
      <InputText
        v-model="query"
        placeholder="Search projects and tasks"
        autofocus
      />
    </span>

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

    <div
      v-else-if="hasSearched && !hasResults"
      class="empty-state"
    >
      No results found.
    </div>

    <div
      v-else
      class="results-grid"
    >
      <Card>
        <template #title>
          Projects
        </template>
        <template #content>
          <div
            v-if="results.projects.length === 0"
            class="muted"
          >
            No matching projects.
          </div>
          <button
            v-for="project in results.projects"
            :key="project.id"
            class="result-row"
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
          Tasks
        </template>
        <template #content>
          <div
            v-if="results.tasks.length === 0"
            class="muted"
          >
            No matching tasks.
          </div>
          <button
            v-for="task in results.tasks"
            :key="task.id"
            class="result-row"
            type="button"
            @click="router.push({ name: 'project', params: { id: task.project_id } })"
          >
            <strong>{{ task.title }}</strong>
            <span>{{ task.project_name }}</span>
            <Tag
              :value="statusLabels[task.status]"
              :severity="statusSeverity[task.status]"
            />
          </button>
        </template>
      </Card>
    </div>
  </section>
</template>

<style scoped>
.search-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.search-page h1 {
  margin: 0;
}

.search-page p {
  margin: 0.25rem 0 0;
  opacity: 0.65;
}

.search-box {
  align-items: center;
  display: flex;
  gap: 0.75rem;
  max-width: 36rem;
}

.search-box :deep(.p-inputtext) {
  width: 100%;
}

.loading-state {
  display: flex;
  justify-content: center;
  padding: 3rem;
}

.results-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.result-row {
  width: 100%;
  border: 0;
  border-bottom: 1px solid #e5e7eb;
  background: transparent;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  padding: 0.85rem 0;
  text-align: left;
}

.result-row:last-child {
  border-bottom: 0;
}

.result-row span,
.muted,
.empty-state {
  color: #6b7280;
}

.empty-state {
  padding: 2rem 0;
}

@media (max-width: 900px) {
  .results-grid {
    grid-template-columns: 1fr;
  }
}
</style>
