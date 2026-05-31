<script setup>
import Button from 'primevue/button'
import Column from 'primevue/column'
import DataTable from 'primevue/datatable'
import Dialog from 'primevue/dialog'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import ProgressSpinner from 'primevue/progressspinner'
import Textarea from 'primevue/textarea'

import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useProjectsStore } from '../stores/projects'

const projectsStore = useProjectsStore()
const router = useRouter()

const dialogVisible = ref(false)
const editingProject = ref(null)
const formError = ref('')
const form = reactive({
  name: '',
  description: '',
})

const dialogTitle = computed(() =>
  editingProject.value ? 'Edit project' : 'New project',
)

const resetForm = () => {
  editingProject.value = null
  form.name = ''
  form.description = ''
  formError.value = ''
}

const openCreateDialog = () => {
  resetForm()
  dialogVisible.value = true
}

const openEditDialog = (project) => {
  editingProject.value = project
  form.name = project.name
  form.description = project.description || ''
  formError.value = ''
  dialogVisible.value = true
}

const closeDialog = () => {
  dialogVisible.value = false
  resetForm()
}

const getValidationMessage = (error) =>
  error.response?.data?.errors?.name?.[0] ||
  error.response?.data?.errors?.description?.[0] ||
  error.response?.data?.message ||
  'Unable to save project.'

const saveProject = async () => {
  formError.value = ''

  try {
    const payload = {
      name: form.name,
      description: form.description,
    }

    if (editingProject.value) {
      await projectsStore.update(editingProject.value.id, payload)
    } else {
      await projectsStore.create(payload)
    }

    closeDialog()
  } catch (error) {
    formError.value = getValidationMessage(error)
  }
}

const removeProject = async (project) => {
  const confirmed = window.confirm(`Delete "${project.name}"?`)

  if (!confirmed) {
    return
  }

  await projectsStore.remove(project.id)
}

const openProject = (event) => {
  router.push({ name: 'project', params: { id: event.data.id } })
}

onMounted(() => {
  projectsStore.fetchProjects()
})
</script>

<template>
  <section class="projects-page">
    <header class="projects-header">
      <div>
        <h1>Projects</h1>
        <p>Manage your active TaskForge projects.</p>
      </div>

      <Button
        label="New project"
        icon="pi pi-plus"
        @click="openCreateDialog"
      />
    </header>

    <Message
      v-if="projectsStore.error"
      severity="error"
      size="small"
    >
      {{ projectsStore.error }}
    </Message>

    <div
      v-if="projectsStore.loading"
      class="loading-state"
    >
      <ProgressSpinner />
    </div>

    <DataTable
      v-else
      :value="projectsStore.projects"
      dataKey="id"
      stripedRows
      responsiveLayout="scroll"
      emptyMessage="No projects yet."
      rowHover
      @row-click="openProject"
    >
      <Column
        field="name"
        header="Name"
      />

      <Column
        field="description"
        header="Description"
      >
        <template #body="{ data }">
          <span class="description">
            {{ data.description || 'No description' }}
          </span>
        </template>
      </Column>

      <Column
        field="created_at"
        header="Created"
      >
        <template #body="{ data }">
          {{ new Date(data.created_at).toLocaleDateString() }}
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
            aria-label="Edit project"
            @click.stop="openEditDialog(data)"
          />
          <Button
            icon="pi pi-trash"
            severity="danger"
            text
            rounded
            aria-label="Delete project"
            @click.stop="removeProject(data)"
          />
        </template>
      </Column>
    </DataTable>

    <Dialog
      v-model:visible="dialogVisible"
      :header="dialogTitle"
      modal
      :style="{ width: 'min(36rem, 92vw)' }"
      @hide="resetForm"
    >
      <div class="project-form">
        <Message
          v-if="formError"
          severity="error"
          size="small"
        >
          {{ formError }}
        </Message>

        <label for="project-name">Name</label>
        <InputText
          id="project-name"
          v-model="form.name"
          autocomplete="off"
        />

        <label for="project-description">Description</label>
        <Textarea
          id="project-description"
          v-model="form.description"
          rows="5"
          autoResize
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
          :loading="projectsStore.saving"
          @click="saveProject"
        />
      </template>
    </Dialog>
  </section>
</template>

<style scoped>
.projects-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.projects-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.projects-header h1 {
  margin: 0;
}

.projects-header p {
  margin: 0.25rem 0 0;
  color: #9dbbda;
}

.loading-state {
  display: flex;
  justify-content: center;
  padding: 3rem;
}

.description {
  color: #8fb0d2;
}

.project-form {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.project-form label {
  font-weight: 600;
}

:deep(.actions-cell) {
  display: flex;
  gap: 0.25rem;
}

:deep(.p-datatable-tbody > tr) {
  cursor: pointer;
}

@media (max-width: 640px) {
  .projects-header {
    align-items: stretch;
    flex-direction: column;
  }
}
</style>
