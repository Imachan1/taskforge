<script setup>
import Message from 'primevue/message'
import ProgressSpinner from 'primevue/progressspinner'

import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const errorMessage = ref('')

const decodeUser = (encodedUser) => {
  const normalized = encodedUser.replace(/-/g, '+').replace(/_/g, '/')
  const padded = normalized.padEnd(
    normalized.length + ((4 - (normalized.length % 4)) % 4),
    '=',
  )

  return JSON.parse(atob(padded))
}

onMounted(async () => {
  try {
    const token = route.query.token
    const encodedUser = route.query.user

    if (!token || !encodedUser) {
      throw new Error('Missing Google authentication payload.')
    }

    auth.setSession({
      token,
      user: decodeUser(encodedUser),
    })

    await router.replace({ name: 'dashboard' })
  } catch (error) {
    errorMessage.value = error.message || 'Unable to complete Google sign in.'
  }
})
</script>

<template>
  <section class="callback-page">
    <Message
      v-if="errorMessage"
      severity="error"
      size="small"
    >
      {{ errorMessage }}
    </Message>

    <template v-else>
      <ProgressSpinner />
      <p>Completing Google sign in...</p>
    </template>
  </section>
</template>

<style scoped>
.callback-page {
  align-items: center;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  justify-content: center;
  min-height: 18rem;
}
</style>
