<script setup>
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'

import { ref } from 'vue'

const email = ref('')
const password = ref('')
</script>

<template>
  <div class="login-page">
    <h1>TaskForge</h1>

    <p class="subtitle">
      Sign in to continue
    </p>

    <div class="form">
      <InputText
        v-model="email"
        placeholder="Email"
      />

      <Password
        v-model="password"
        placeholder="Password"
        :feedback="false"
        toggleMask
      />

      <Button
        label="Sign In"
        fluid
      />

      <div class="divider">
        OR
      </div>

      <Button
        label="Continue with Google"
        severity="secondary"
        fluid
      />
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
</style>