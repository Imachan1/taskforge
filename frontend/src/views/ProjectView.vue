<script setup>
import Button from 'primevue/button'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import ProgressSpinner from 'primevue/progressspinner'
import Select from 'primevue/select'
import Tag from 'primevue/tag'
import Textarea from 'primevue/textarea'

import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getProject } from '../api/projects'
import { useTasksStore } from '../stores/tasks'

const route = useRoute()
const router = useRouter()
const tasksStore = useTasksStore()

const project = ref(null)
const projectLoading = ref(false)
const pageError = ref('')
const dialogVisible = ref(false)
const editingTask = ref(null)
const formError = ref('')

const form = reactive({
  title: '',
  description: '',
  status: 'todo',
  priority: 'medium',
  due_date: '',
})

const statusOptions = [
  { label: 'Todo', value: 'todo', severity: 'secondary' },
  { label: 'In Progress', value: 'in_progress', severity: 'info' },
  { label: 'Done', value: 'done', severity: 'success' },
]

const priorityOptions = [
  { label: 'Low', value: 'low', severity: 'success' },
  { label: 'Medium', value: 'medium', severity: 'warn' },
  { label: 'High', value: 'high', severity: 'danger' },
]

const dialogTitle = computed(() =>
  editingTask.value ? 'Edit task' : 'New task',
)

const currentProjectId = computed(() => route.params.id)

const findOption = (options, value) =>
  options.find((option) => option.value === value) || options[0]

const resetForm = () => {
  editingTask.value = null
  form.title = ''
  form.description = ''
  form.status = 'todo'
  form.priority = 'medium'
  form.due_date = ''
  formError.value = ''
}

const getTaskPayload = (task = form) => ({
  title: task.title,
  description: task.description || null,
  status: task.status,
  priority: task.priority,
  due_date: task.due_date || null,
})

const getValidationMessage = (error) =>
  error.response?.data?.errors?.title?.[0] ||
  error.response?.data?.errors?.description?.[0] ||
  error.response?.data?.errors?.status?.[0] ||
  error.response?.data?.errors?.priority?.[0] ||
  error.response?.data?.errors?.due_date?.[0] ||
  error.response?.data?.message ||
  'Unable to save task.'

const loadProject = async () => {
  projectLoading.value = true
  pageError.value = ''
  tasksStore.clear()

  try {
    const [{ data }] = await Promise.all([
      getProject(currentProjectId.value),
      tasksStore.fetchProjectTasks(currentProjectId.value),
    ])

    project.value = data.data
  } catch (error) {
    pageError.value =
      error.response?.data?.message || 'Unable to load project tasks.'
  } finally {
    projectLoading.value = false
  }
}

const openCreateDialog = () => {
  resetForm()
  dialogVisible.value = true
}

const openEditDialog = (task) => {
  editingTask.value = task
  form.title = task.title
  form.description = task.description || ''
  form.status = task.status
  form.priority = task.priority
  form.due_date = task.due_date || ''
  formError.value = ''
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  resetForm()
}

const saveTask = async () => {
  formError.value = ''

  try {
    if (editingTask.value) {
      await tasksStore.update(editingTask.value.id, getTaskPayload())
    } else {
      await tasksStore.create(currentProjectId.value, getTaskPayload())
    }

    closeDialog()
  } catch (error) {
    formError.value = getValidationMessage(error)
  }
}

const updateTaskField = async (task, field, value) => {
  await tasksStore.update(task.id, {
    ...getTaskPayload(task),
    [field]: value,
  })
}

const removeTask = async (task) => {
  const confirmed = window.confirm(`Delete "${task.title}"?`)

  if (!confirmed) {
    return
  }

  await tasksStore.remove(task.id)
}

onMounted(loadProject)
</script>

<template>
  <section class="project-page">
    <Button
      label="Back to projects"
      icon="pi pi-arrow-left"
      severity="secondary"
      text
      class="back-button"
      @click="router.push({ name: 'projects' })"
    />

    <Message
      v-if="pageError"
      severity="error"
      size="small"
    >
      {{ pageError }}
    </Message>

    <div
      v-if="projectLoading"
      class="loading-state"
    >
      <ProgressSpinner />
    </div>

    <template v-else-if="project">
      <header class="project-header">
        <div>
          <h1>{{ project.name }}</h1>
          <p>{{ project.description || 'No project description' }}</p>
        </div>

        <Button
          label="New task"
          icon="pi pi-plus"
          @click="openCreateDialog"
        />
      </header>

      <Message
        v-if="tasksStore.error"
        severity="error"
        size="small"
      >
        {{ tasksStore.error }}
      </Message>

      <div
        v-if="tasksStore.loading"
        class="loading-state"
      >
        <ProgressSpinner />
      </div>

      <DataTable
        v-else
        :value="tasksStore.tasks"
        dataKey="id"
        stripedRows
        responsiveLayout="scroll"
        emptyMessage="No tasks yet."
      >
        <Column
          field="title"
          header="Task"
        >
          <template #body="{ data }">
            <div class="task-title">
              <strong>{{ data.title }}</strong>
              <span>{{ data.description || 'No description' }}</span>
            </div>
          </template>
        </Column>

        <Column header="Status">
          <template #body="{ data }">
            <div class="inline-control">
              <Tag
                :value="findOption(statusOptions, data.status).label"
                :severity="findOption(statusOptions, data.status).severity"
              />
              <Select
                :modelValue="data.status"
                :options="statusOptions"
                optionLabel="label"
                optionValue="value"
                size="small"
                @update:modelValue="updateTaskField(data, 'status', $event)"
              />
            </div>
          </template>
        </Column>

        <Column header="Priority">
          <template #body="{ data }">
            <div class="inline-control">
              <Tag
                :value="findOption(priorityOptions, data.priority).label"
                :severity="findOption(priorityOptions, data.priority).severity"
              />
              <Select
                :modelValue="data.priority"
                :options="priorityOptions"
                optionLabel="label"
                optionValue="value"
                size="small"
                @update:modelValue="updateTaskField(data, 'priority', $event)"
              />
            </div>
          </template>
        </Column>

        <Column
          field="due_date"
          header="Due"
        >
          <template #body="{ data }">
            {{ data.due_date || 'No due date' }}
          </template>
        </Column>

        <Column
          header="Actions"
          bodyClass="actions-cell"
        >
          <template #body="{ data }">
            <Button
              icon="pi pi-pencil"
              severity="secondary"
              text
              rounded
              aria-label="Edit task"
              @click="openEditDialog(data)"
            />
            <Button
              icon="pi pi-trash"
              severity="danger"
              text
              rounded
              aria-label="Delete task"
              @click="removeTask(data)"
            />
          </template>
        </Column>
      </DataTable>
    </template>

    <Dialog
      v-model:visible="dialogVisible"
      :header="dialogTitle"
      modal
      :style="{ width: 'min(38rem, 92vw)' }"
      @hide="resetForm"
    >
      <div class="task-form">
        <Message
          v-if="formError"
          severity="error"
          size="small"
        >
          {{ formError }}
        </Message>

        <label for="task-title">Title</label>
        <InputText
          id="task-title"
          v-model="form.title"
          autocomplete="off"
        />

        <label for="task-description">Description</label>
        <Textarea
          id="task-description"
          v-model="form.description"
          rows="4"
          autoResize
        />

        <div class="form-grid">
          <div>
            <label for="task-status">Status</label>
            <Select
              id="task-status"
              v-model="form.status"
              :options="statusOptions"
              optionLabel="label"
              optionValue="value"
            />
          </div>

          <div>
            <label for="task-priority">Priority</label>
            <Select
              id="task-priority"
              v-model="form.priority"
              :options="priorityOptions"
              optionLabel="label"
              optionValue="value"
            />
          </div>
        </div>

        <label for="task-due-date">Due date</label>
        <InputText
          id="task-due-date"
          v-model="form.due_date"
          placeholder="YYYY-MM-DD"
          autocomplete="off"
        />
      </div>

      <template #footer>
        <Button
          label="Cancel"
          severity="secondary"
          text
          @click="closeDialog"
        />
        <Button
          label="Save"
          icon="pi pi-check"
          :loading="tasksStore.saving"
          @click="saveTask"
        />
      </template>
    </Dialog>
  </section>
</template>

<style scoped>
.project-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.back-button {
  align-self: flex-start;
}

.project-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.project-header h1 {
  margin: 0;
}

.project-header p {
  margin: 0.25rem 0 0;
  color: #9dbbda;
}

.loading-state {
  display: flex;
  justify-content: center;
  padding: 3rem;
}

.task-title {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.task-title span {
  color: #8fb0d2;
}

.inline-control {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.task-form {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.task-form label {
  display: block;
  margin-bottom: 0.35rem;
  font-weight: 600;
}

.form-grid {
  display: grid;
  gap: 0.75rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

:deep(.actions-cell) {
  display: flex;
  gap: 0.25rem;
}

@media (max-width: 720px) {
  .project-header {
    align-items: stretch;
    flex-direction: column;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
