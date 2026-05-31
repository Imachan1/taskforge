<script setup>
import Avatar from 'primevue/avatar'
import Button from 'primevue/button'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext'
import Message from 'primevue/message'
import Password from 'primevue/password'

import { computed, onMounted, reactive, ref } from 'vue'
import { getProfile, updatePassword, updateProfile } from '../api/profile'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()

const loading = ref(false)
const profileError = ref('')
const profileSuccess = ref('')
const passwordError = ref('')
const passwordSuccess = ref('')
const savingProfile = ref(false)
const savingPassword = ref(false)

const profile = ref(null)
const profileForm = reactive({
  name: '',
})
const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const profileErrors = ref({})
const passwordErrors = ref({})

const initials = computed(() => {
  const name = profile.value?.name || profile.value?.email || 'U'
  return name
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})

const profileValidationMessages = computed(() =>
  Object.values(profileErrors.value).flat(),
)
const passwordValidationMessages = computed(() =>
  Object.values(passwordErrors.value).flat(),
)

const registrationDate = computed(() => {
  if (!profile.value?.created_at) {
    return ''
  }

  return new Date(profile.value.created_at).toLocaleDateString()
})

const loadProfile = async () => {
  loading.value = true
  profileError.value = ''

  try {
    const { data } = await getProfile()
    profile.value = data.user
    profileForm.name = data.user.name
    auth.setUser(data.user)
  } catch (error) {
    profileError.value = error.response?.data?.message || 'Unable to load profile.'
  } finally {
    loading.value = false
  }
}

const saveProfile = async () => {
  savingProfile.value = true
  profileError.value = ''
  profileSuccess.value = ''
  profileErrors.value = {}

  try {
    const { data } = await updateProfile({
      name: profileForm.name,
    })
    profile.value = data.user
    profileSuccess.value = data.message
    auth.setUser(data.user)
  } catch (error) {
    profileErrors.value = error.response?.data?.errors || {}
    profileError.value =
      error.response?.data?.message || 'Unable to update profile.'
  } finally {
    savingProfile.value = false
  }
}

const changePassword = async () => {
  savingPassword.value = true
  passwordError.value = ''
  passwordSuccess.value = ''
  passwordErrors.value = {}

  try {
    const { data } = await updatePassword(passwordForm)
    passwordSuccess.value = data.message
    passwordForm.current_password = ''
    passwordForm.password = ''
    passwordForm.password_confirmation = ''
  } catch (error) {
    passwordErrors.value = error.response?.data?.errors || {}
    passwordError.value =
      error.response?.data?.message || 'Unable to update password.'
  } finally {
    savingPassword.value = false
  }
}

onMounted(loadProfile)
</script>

<template>
  <section class="profile-page">
    <header>
      <h1>Profile</h1>
      <p>Manage your account details and password.</p>
    </header>

    <Message
      v-if="profileError && !profile"
      severity="error"
      size="small"
    >
      {{ profileError }}
    </Message>

    <div
      v-if="loading"
      class="loading-state"
    >
      Loading profile...
    </div>

    <template v-else-if="profile">
      <Card>
        <template #content>
          <div class="profile-summary">
            <Avatar
              v-if="profile.avatar_url"
              :image="profile.avatar_url"
              shape="circle"
              size="xlarge"
            />
            <Avatar
              v-else
              :label="initials"
              shape="circle"
              size="xlarge"
            />

            <div>
              <h2>{{ profile.name }}</h2>
              <p>{{ profile.email }}</p>
              <span>Registered {{ registrationDate }}</span>
            </div>
          </div>
        </template>
      </Card>

      <div class="profile-grid">
        <Card>
          <template #title>
            Edit Profile
          </template>
          <template #content>
            <form
              class="profile-form"
              @submit.prevent="saveProfile"
            >
              <Message
                v-if="profileSuccess"
                severity="success"
                size="small"
              >
                {{ profileSuccess }}
              </Message>
              <Message
                v-if="profileError"
                severity="error"
                size="small"
              >
                {{ profileError }}
              </Message>
              <Message
                v-for="message in profileValidationMessages"
                :key="message"
                severity="error"
                size="small"
              >
                {{ message }}
              </Message>

              <label for="profile-name">Name</label>
              <InputText
                id="profile-name"
                v-model="profileForm.name"
                autocomplete="name"
              />

              <label for="profile-email">Email</label>
              <InputText
                id="profile-email"
                :modelValue="profile.email"
                disabled
              />

              <Button
                label="Save profile"
                icon="pi pi-check"
                type="submit"
                :loading="savingProfile"
              />
            </form>
          </template>
        </Card>

        <Card>
          <template #title>
            Change Password
          </template>
          <template #content>
            <form
              class="profile-form"
              @submit.prevent="changePassword"
            >
              <Message
                v-if="passwordSuccess"
                severity="success"
                size="small"
              >
                {{ passwordSuccess }}
              </Message>
              <Message
                v-if="passwordError"
                severity="error"
                size="small"
              >
                {{ passwordError }}
              </Message>
              <Message
                v-for="message in passwordValidationMessages"
                :key="message"
                severity="error"
                size="small"
              >
                {{ message }}
              </Message>

              <Password
                v-model="passwordForm.current_password"
                placeholder="Current Password"
                autocomplete="current-password"
                :feedback="false"
                toggleMask
              />

              <Password
                v-model="passwordForm.password"
                placeholder="New Password"
                autocomplete="new-password"
                :feedback="false"
                toggleMask
              />

              <Password
                v-model="passwordForm.password_confirmation"
                placeholder="Confirm Password"
                autocomplete="new-password"
                :feedback="false"
                toggleMask
              />

              <Button
                label="Change password"
                icon="pi pi-lock"
                type="submit"
                :loading="savingPassword"
              />
            </form>
          </template>
        </Card>
      </div>
    </template>
  </section>
</template>

<style scoped>
.profile-page {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.profile-page h1,
.profile-page h2 {
  margin: 0;
}

.profile-page p {
  margin: 0.25rem 0 0;
  color: #9dbbda;
}

.loading-state {
  padding: 2rem 0;
}

.profile-summary {
  align-items: center;
  display: flex;
  gap: 1rem;
}

.profile-summary span {
  color: #8aa8c9;
  display: inline-block;
  margin-top: 0.5rem;
}

.profile-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.profile-form label {
  font-weight: 600;
}

.profile-form button {
  align-self: flex-start;
}

@media (max-width: 900px) {
  .profile-grid {
    grid-template-columns: 1fr;
  }
}
</style>
