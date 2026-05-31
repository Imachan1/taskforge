<script setup>
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'
import Message from 'primevue/message'

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)

const router = useRouter()
const auth = useAuthStore()
const backendUrl = import.meta.env.VITE_BACKEND_URL || ''

const signIn = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    await auth.login({
      email: email.value,
      password: password.value,
    })

    await router.push({ name: 'dashboard' })
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message ||
      error.response?.data?.errors?.email?.[0] ||
      'Unable to sign in. Please try again.'
  } finally {
    loading.value = false
  }
}

const signInWithGoogle = () => {
  if (!backendUrl) {
    errorMessage.value = 'Google sign in is not configured.'
    return
  }

  window.location.href = `${backendUrl}/auth/google/redirect`
}
</script>

<template>
  <div class="login-page">
    <h1>TaskForge</h1>

    <p class="subtitle">
      Sign in to continue
    </p>

    <div class="form">
      <Message
        v-if="errorMessage"
        severity="error"
        size="small"
      >
        {{ errorMessage }}
      </Message>

      <InputText
        v-model="email"
        placeholder="Email"
        autocomplete="email"
      />

      <Password
        v-model="password"
        placeholder="Password"
        autocomplete="current-password"
        :feedback="false"
        toggleMask
        @keyup.enter="signIn"
      />

      <Button
        label="Sign In"
        fluid
        :loading="loading"
        @click="signIn"
      />

      <div class="divider">
        OR
      </div>

      <Button
        label="Continue with Google"
        icon="pi pi-google"
        severity="secondary"
        fluid
        @click="signInWithGoogle"
      />

      <p class="auth-link">
        Don't have an account?
        <RouterLink :to="{ name: 'register' }">
          Register
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<style scoped>
.login-page {
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

.divider {
  text-align: center;
  opacity: 0.5;
}

.auth-link {
  margin: 0;
  text-align: center;
}
</style>
