<script setup>
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import Password from 'primevue/password'

import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const registerErrorMessage = ref('')
const registerSuccessMessage = ref('')
const loading = ref(false)
const registering = ref(false)
const isRegistering = ref(false)
const registerErrors = ref({})
const registerForm = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()

const normalizeUrl = (value) => value?.trim().replace(/\/+$/, '') || ''

const resolveBackendUrl = () => {
  const configuredBackendUrl = normalizeUrl(import.meta.env.VITE_BACKEND_URL)

  if (configuredBackendUrl) {
    return configuredBackendUrl
  }

  const configuredApiUrl = normalizeUrl(import.meta.env.VITE_API_URL)

  if (configuredApiUrl && /^https?:\/\//.test(configuredApiUrl)) {
    return configuredApiUrl.replace(/\/api(?:\/)?$/, '')
  }

  const { protocol, hostname, origin } = window.location

  if (hostname === 'localhost' || hostname === '127.0.0.1') {
    return `${protocol}//127.0.0.1:8000`
  }

  return origin
}

const backendUrl = resolveBackendUrl()

const registerValidationMessages = computed(() =>
  Object.values(registerErrors.value).flat(),
)

const resetRegisterForm = () => {
  registerForm.name = ''
  registerForm.email = ''
  registerForm.password = ''
  registerForm.password_confirmation = ''
  registerErrors.value = {}
  registerErrorMessage.value = ''
}

const showRegister = () => {
  errorMessage.value = ''
  registerSuccessMessage.value = ''
  isRegistering.value = true
}

const showLogin = () => {
  registerErrorMessage.value = ''
  isRegistering.value = false
}

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

  const params = new URLSearchParams({
    frontend_url: window.location.origin,
  })

  window.location.href = `${backendUrl}/auth/google/redirect?${params.toString()}`
}

const register = async () => {
  registerErrors.value = {}
  registerErrorMessage.value = ''
  registerSuccessMessage.value = ''
  registering.value = true

  try {
    await auth.register(registerForm)
    await auth.logout()
    resetRegisterForm()
    registerSuccessMessage.value = 'Account created. Sign in to continue.'
    isRegistering.value = false
  } catch (error) {
    registerErrors.value = error.response?.data?.errors || {}
    registerErrorMessage.value =
      error.response?.data?.message || 'Unable to register. Please try again.'
  } finally {
    registering.value = false
  }
}

onMounted(() => {
  isRegistering.value = route.query.mode === 'register'
})
</script>

<template>
  <main class="auth-shell">
    <section class="brand-panel">
      <p class="eyebrow">Project command center</p>
      <h1>TASKFORGE</h1>
      <p class="brand-copy">
        Plan launches, shape tasks, and keep execution moving inside a focused
        SaaS workspace.
      </p>
    </section>

    <div
      class="card-stage"
      :class="{ 'is-flipped': isRegistering }"
    >
      <div class="auth-card">
        <section class="card-face card-face--front">
          <div class="card-header">
            <p class="kicker">Welcome back</p>
            <h2>Login</h2>
            <span class="single-line-subtitle">Enter your workspace credentials.</span>
          </div>

          <div class="form">
            <Message
              v-if="errorMessage"
              severity="error"
              size="small"
            >
              {{ errorMessage }}
            </Message>

            <Message
              v-if="registerSuccessMessage"
              severity="success"
              size="small"
            >
              {{ registerSuccessMessage }}
            </Message>

            <div class="field">
              <label for="login-email">Email</label>
              <InputText
                id="login-email"
                v-model="email"
                placeholder="you@company.com"
                autocomplete="email"
              />
            </div>

            <div class="field">
              <label for="login-password">Password</label>
              <Password
                inputId="login-password"
                v-model="password"
                placeholder="Password"
                autocomplete="current-password"
                :feedback="false"
                toggleMask
                @keyup.enter="signIn"
              />
            </div>

            <Button
              label="Sign In"
              class="primary-action"
              fluid
              :loading="loading"
              @click="signIn"
            />

            <div class="divider">
              <span>OR</span>
            </div>

            <button
              class="google-button"
              type="button"
              @click="signInWithGoogle"
            >
              <svg
                aria-hidden="true"
                viewBox="0 0 24 24"
              >
                <path
                  fill="#4285F4"
                  d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                />
                <path
                  fill="#34A853"
                  d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                />
                <path
                  fill="#FBBC05"
                  d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"
                />
                <path
                  fill="#EA4335"
                  d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06L5.84 9.9C6.71 7.3 9.14 5.38 12 5.38z"
                />
              </svg>
              Continue with Google
            </button>

            <p class="auth-link">
              Don't have an account?
              <button
                type="button"
                @click="showRegister"
              >
                Register
              </button>
            </p>
          </div>
        </section>

        <section class="card-face card-face--back">
          <div class="card-header">
            <p class="kicker">Start building</p>
            <h2>Register</h2>
            <span>Create a TaskForge account.</span>
          </div>

          <div class="form">
            <Message
              v-if="registerErrorMessage"
              severity="error"
              size="small"
            >
              {{ registerErrorMessage }}
            </Message>

            <Message
              v-for="message in registerValidationMessages"
              :key="message"
              severity="error"
              size="small"
            >
              {{ message }}
            </Message>

            <div class="field">
              <label for="register-name">Name</label>
              <InputText
                id="register-name"
                v-model="registerForm.name"
                placeholder="Your name"
                autocomplete="name"
              />
            </div>

            <div class="field">
              <label for="register-email">Email</label>
              <InputText
                id="register-email"
                v-model="registerForm.email"
                placeholder="you@company.com"
                autocomplete="email"
              />
            </div>

            <div class="field">
              <label for="register-password">Password</label>
              <Password
                inputId="register-password"
                v-model="registerForm.password"
                placeholder="At least 8 characters"
                autocomplete="new-password"
                :feedback="false"
                toggleMask
              />
            </div>

            <div class="field">
              <label for="register-password-confirmation">Confirm Password</label>
              <Password
                inputId="register-password-confirmation"
                v-model="registerForm.password_confirmation"
                placeholder="Repeat password"
                autocomplete="new-password"
                :feedback="false"
                toggleMask
                @keyup.enter="register"
              />
            </div>

            <Button
              label="Register"
              class="primary-action"
              fluid
              :loading="registering"
              @click="register"
            />

            <p class="auth-link">
              Already have an account?
              <button
                type="button"
                @click="showLogin"
              >
                Sign In
              </button>
            </p>
          </div>
        </section>
      </div>
    </div>
  </main>
</template>

<style scoped>
.auth-shell {
  width: min(100%, 76rem);
  display: grid;
  grid-template-columns: minmax(0, 0.92fr) minmax(22rem, 29rem);
  gap: clamp(2rem, 6vw, 6rem);
  align-items: center;
}

.auth-shell,
.auth-shell * {
  box-sizing: border-box;
}

.brand-panel {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  color: #e0f2fe;
  min-width: 0;
}

.eyebrow,
.kicker {
  margin: 0;
  color: #67e8f9;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

.brand-panel h1 {
  margin: 0;
  color: #f8fbff;
  font-family: 'Orbitron', 'Rajdhani', 'Segoe UI', sans-serif;
  font-size: clamp(3rem, 6vw, 5rem);
  font-weight: 900;
  letter-spacing: 0.18em;
  line-height: 0.9;
  text-shadow:
    0 0 24px rgba(56, 189, 248, 0.42),
    0 0 72px rgba(37, 99, 235, 0.3);
}

.brand-copy {
  max-width: 35rem;
  margin: 0;
  color: #a9c6e8;
  font-size: clamp(1rem, 2vw, 1.18rem);
  line-height: 1.8;
}

.card-stage {
  width: 100%;
  max-width: 29rem;
  justify-self: center;
  min-height: 42rem;
  min-width: 0;
  perspective: 1800px;
}

.auth-card {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 42rem;
  min-width: 0;
  transform-style: preserve-3d;
  transition: transform 760ms cubic-bezier(0.22, 1, 0.36, 1);
  will-change: transform;
}

.card-stage.is-flipped .auth-card {
  transform: rotateY(180deg);
}

.card-face {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 1.5rem;
  padding: clamp(1.35rem, 4vw, 2.4rem);
  overflow: hidden;
  min-width: 0;
  backface-visibility: hidden;
  border: 1px solid rgba(125, 211, 252, 0.16);
  border-radius: 30px;
  background:
    linear-gradient(148deg, rgba(10, 28, 56, 0.08), rgba(16, 46, 82, 0.02)),
    rgba(7, 18, 36, 0.01);
  box-shadow:
    0 14px 34px rgba(0, 0, 0, 0.14),
    0 0 0 1px rgba(56, 189, 248, 0.06) inset,
    0 0 28px rgba(14, 165, 233, 0.06);
  backdrop-filter: blur(10px) saturate(115%) brightness(1.02);
  -webkit-backdrop-filter: blur(10px) saturate(115%) brightness(1.02);
  pointer-events: none;
}

.card-face::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    linear-gradient(122deg, transparent 0%, rgba(186, 230, 253, 0.13) 42%, transparent 60%),
    radial-gradient(circle at 14% -10%, rgba(255, 255, 255, 0.1), transparent 40%);
  opacity: 0.04;
  pointer-events: none;
}

.card-face::after {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 0.75px 0.9px, rgba(255, 255, 255, 0.13) 0 0.6px, transparent 0.75px),
    radial-gradient(circle at 1.2px 1.1px, rgba(255, 255, 255, 0.08) 0 0.45px, transparent 0.7px),
    radial-gradient(110% 86% at 82% 74%, rgba(255, 255, 255, 0.1), transparent 56%);
  background-size: 4.2px 4.2px, 5px 5px, auto;
  background-position: 0 0, 1.1px 1.3px, 0 0;
  opacity: 0.01;
  mix-blend-mode: screen;
  pointer-events: none;
}

.card-face--back {
  transform: rotateY(180deg);
}

.card-stage:not(.is-flipped) .card-face--front,
.card-stage.is-flipped .card-face--back {
  pointer-events: auto;
}

.card-header,
.form {
  position: relative;
  z-index: 1;
}

.card-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.45rem;
  text-align: center;
}

.card-header h2 {
  margin: 0;
  color: #f8fbff;
  font-size: clamp(2rem, 5vw, 2.8rem);
  letter-spacing: 0.02em;
}

.card-header span {
  color: #93b7db;
  max-width: 24ch;
}

.card-header .single-line-subtitle {
  max-width: none;
  white-space: nowrap;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 0.95rem;
  min-width: 0;
  pointer-events: auto;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
  min-width: 0;
  pointer-events: auto;
}

.field label {
  color: #c7ddf4;
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.field :deep(.p-inputtext),
.field :deep(.p-password),
.field :deep(.p-password-input) {
  width: 100%;
}

.field :deep(.p-inputtext),
.field :deep(.p-password-input) {
  min-height: 3rem;
  color: #eaf6ff;
  border: 1px solid rgba(148, 197, 255, 0.22);
  border-radius: 16px;
  background: rgba(4, 16, 33, 0.54);
  box-shadow: 0 0 0 rgba(56, 189, 248, 0);
  transition:
    border-color 180ms ease,
    box-shadow 180ms ease,
    background 180ms ease,
    transform 180ms ease;
}

.field :deep(.p-inputtext::placeholder),
.field :deep(.p-password-input::placeholder) {
  color: #6f8fb0;
}

.field :deep(.p-inputtext:focus),
.field :deep(.p-password-input:focus) {
  border-color: rgba(56, 189, 248, 0.9);
  background: rgba(6, 24, 48, 0.74);
  box-shadow:
    0 0 0 4px rgba(14, 165, 233, 0.16),
    0 0 24px rgba(56, 189, 248, 0.18);
  transform: translateY(-1px);
}

.primary-action {
  margin-top: 0.35rem;
  min-height: 3.1rem;
  border: 1px solid rgba(125, 211, 252, 0.35) !important;
  border-radius: 16px !important;
  background:
    linear-gradient(135deg, #0ea5e9 0%, #2563eb 55%, #0b4bd5 100%) !important;
  color: #ffffff !important;
  box-shadow: 0 14px 34px rgba(37, 99, 235, 0.36);
  transition:
    transform 180ms ease,
    box-shadow 180ms ease,
    filter 180ms ease;
}

.primary-action :deep(.p-button-label) {
  color: #ffffff;
}

.primary-action:hover {
  filter: brightness(1.08);
  transform: translateY(-2px) scale(1.01);
  box-shadow:
    0 18px 44px rgba(37, 99, 235, 0.48),
    0 0 28px rgba(56, 189, 248, 0.28);
}

.divider {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  color: #6f8fb0;
  font-size: 0.76rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  text-align: center;
}

.divider::before,
.divider::after {
  content: '';
  height: 1px;
  flex: 1;
  background: linear-gradient(90deg, transparent, rgba(125, 211, 252, 0.26), transparent);
}

.google-button {
  min-height: 3.05rem;
  border: 1px solid rgba(226, 242, 255, 0.18);
  border-radius: 16px;
  background: rgba(224, 242, 254, 0.08);
  color: #eaf6ff;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  font: inherit;
  font-weight: 700;
  transition:
    border-color 180ms ease,
    background 180ms ease,
    box-shadow 180ms ease,
    transform 180ms ease;
}

.google-button svg {
  width: 1.2rem;
  height: 1.2rem;
}

.google-button:hover {
  border-color: rgba(125, 211, 252, 0.45);
  background: rgba(224, 242, 254, 0.13);
  box-shadow: 0 0 26px rgba(56, 189, 248, 0.15);
  transform: translateY(-2px) scale(1.01);
}

.auth-link {
  margin: 0;
  color: #93b7db;
  text-align: center;
}

.auth-link button {
  border: 0;
  background: transparent;
  color: #7dd3fc;
  cursor: pointer;
  font: inherit;
  font-weight: 800;
  padding: 0.1rem 0.2rem;
}

.auth-link button:hover,
.auth-link button:focus-visible {
  color: #e0f2fe;
  text-shadow: 0 0 18px rgba(56, 189, 248, 0.85);
}

@media (max-width: 1100px) {
  .auth-shell {
    grid-template-columns: 1fr;
    gap: 0.9rem;
    align-content: center;
  }

  .brand-panel {
    text-align: center;
    align-items: center;
    gap: 0.75rem;
  }

  .brand-panel h1 {
    font-size: clamp(2.8rem, 7vw, 4rem);
    letter-spacing: 0.13em;
  }

  .brand-copy {
    max-width: 38rem;
    line-height: 1.55;
  }

  .card-stage,
  .auth-card {
    min-height: 35.8rem;
  }

  .card-face {
    gap: 1rem;
    padding: 1.5rem 2rem;
  }

  .card-header {
    gap: 0.24rem;
  }

  .card-header h2 {
    font-size: 2.2rem;
  }

  .form {
    gap: 0.64rem;
  }

  .field {
    gap: 0.28rem;
  }

  .field :deep(.p-inputtext),
  .field :deep(.p-password-input) {
    min-height: 2.65rem;
  }

  .primary-action,
  .google-button {
    min-height: 2.8rem;
  }
}

@media (max-width: 640px) {
  .auth-shell {
    width: 100%;
    max-width: calc(100vw - 2rem);
    min-height: calc(100vh - 2rem);
    grid-template-columns: minmax(0, 1fr);
    justify-content: center;
    min-width: 0;
    overflow: hidden;
  }

  .brand-panel,
  .card-stage {
    width: 100%;
    max-width: min(100%, 22rem);
  }

  .brand-panel {
    justify-self: center;
  }

  .card-stage {
    justify-self: start;
  }

  .auth-card,
  .card-face,
  .field :deep(.p-inputtext),
  .field :deep(.p-password),
  .field :deep(.p-password-input),
  .primary-action,
  .google-button {
    width: 100%;
    max-width: 100%;
  }

  .brand-panel h1 {
    max-width: 100%;
    font-size: clamp(1.9rem, 9vw, 2.25rem);
    letter-spacing: 0.04em;
    overflow: hidden;
    text-overflow: clip;
  }

  .brand-copy {
    display: none;
  }

  .card-stage,
  .auth-card {
    min-height: 43.5rem;
  }

  .card-face {
    gap: 1.5rem;
    padding: 1.25rem;
    border-radius: 24px;
  }

  .card-header {
    gap: 0.45rem;
  }

  .form {
    gap: 0.95rem;
  }

  .field {
    gap: 0.45rem;
  }

  .field :deep(.p-inputtext),
  .field :deep(.p-password-input) {
    min-height: 3rem;
  }

  .primary-action {
    min-height: 3.1rem;
  }

  .google-button {
    min-height: 3.05rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .auth-card,
  .auth-shell,
  .primary-action,
  .google-button {
    animation: none;
    transition: none;
  }
}
</style>
