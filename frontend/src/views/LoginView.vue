<script setup>
import InputText from 'primevue/inputtext'
import Password from 'primevue/password'
import Button from 'primevue/button'

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')

const auth = useAuthStore()
const router = useRouter()

function signIn() {
  auth.login({
    id: 1,
    name: 'Valeria',
    email: email.value,
  })

  router.push('/dashboard')
}
</script>

<template>
  <div class="login-page">
    <h1>TaskForge</h1>

```
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
    @click="signIn"
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
```

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
