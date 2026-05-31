<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import Password from 'primevue/password'

import { computed, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const errors = ref({})
const generalError = ref('')
const loading = ref(false)

const validationMessages = computed(() =>
  Object.values(errors.value).flat(),
)

const register = async () => {
  errors.value = {}
  generalError.value = ''
  loading.value = true

  try {
    await auth.register(form)
    await router.push({ name: 'dashboard' })
  } catch (error) {
    errors.value = error.response?.data?.errors || {}
    generalError.value =
      error.response?.data?.message || 'Unable to register. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="register-page">
    <h1>Create account</h1>

    <p class="subtitle">
      Start organizing your work in TaskForge
    </p>

    <div class="form">
      <Message
        v-if="generalError"
        severity="error"
        size="small"
      >
        {{ generalError }}
      </Message>

      <Message
        v-for="message in validationMessages"
        :key="message"
        severity="error"
        size="small"
      >
        {{ message }}
      </Message>

      <InputText
        v-model="form.name"
        placeholder="Name"
        autocomplete="name"
      />

      <InputText
        v-model="form.email"
        placeholder="Email"
        autocomplete="email"
      />

      <Password
        v-model="form.password"
        placeholder="Password"
        autocomplete="new-password"
        :feedback="false"
        toggleMask
      />

      <Password
        v-model="form.password_confirmation"
        placeholder="Confirm Password"
        autocomplete="new-password"
        :feedback="false"
        toggleMask
        @keyup.enter="register"
      />

      <Button
        label="Register"
        fluid
        :loading="loading"
        @click="register"
      />

      <p class="auth-link">
        Already have an account?
        <RouterLink :to="{ name: 'login' }">
          Sign In
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.subtitle {
  opacity: 0.7;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.auth-link {
  margin: 0;
  text-align: center;
}
</style>
