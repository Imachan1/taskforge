<script setup>
import Button from 'primevue/button'

import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const initials = computed(() => {
  const name = auth.user?.name || auth.user?.email || 'U'
  return name
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})

const logout = async () => {
  await auth.logout()
  await router.push({ name: 'login' })
}
</script>

<template>
  <aside class="sidebar">
    <div class="sidebar__logo">
      TaskForge
    </div>

    <nav class="sidebar__nav">
      <RouterLink to="/dashboard">Dashboard</RouterLink>

      <RouterLink to="/projects">Projects</RouterLink>

      <RouterLink to="/search">Search</RouterLink>

      <RouterLink to="/profile">Profile</RouterLink>
    </nav>

    <div
      v-if="auth.user"
      class="sidebar__user"
    >
      <img
        v-if="auth.user.avatar_url"
        :src="auth.user.avatar_url"
        alt=""
        class="sidebar__avatar"
      >
      <div
        v-else
        class="sidebar__avatar sidebar__avatar--fallback"
      >
        {{ initials }}
      </div>

      <div class="sidebar__user-text">
        <strong>{{ auth.user.name }}</strong>
        <span>{{ auth.user.email }}</span>
      </div>

      <Button
        label="Logout"
        icon="pi pi-sign-out"
        severity="secondary"
        size="small"
        outlined
        @click="logout"
      />
    </div>
  </aside>
</template>

<style scoped lang="scss">
.sidebar {
  width: 240px;
  min-height: 100vh;

  background: #111827;
  border-right: 1px solid #1f2937;

  display: flex;
  flex-direction: column;
  gap: 24px;

  padding: 24px;
}

.sidebar__logo {
  color: white;
  font-size: 24px;
  font-weight: 700;

  margin-bottom: 32px;
}

.sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.sidebar__nav a {
  color: #d1d5db;

  text-decoration: none;

  padding: 12px 16px;

  border-radius: 8px;

  transition: 0.2s;
}

.sidebar__nav a:hover {
  background: #1f2937;
}

.sidebar__nav a.router-link-active {
  background: #2563eb;
  color: white;
}

.sidebar__user {
  border-top: 1px solid #1f2937;
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding-top: 16px;
}

.sidebar__avatar {
  border-radius: 999px;
  height: 44px;
  object-fit: cover;
  width: 44px;
}

.sidebar__avatar--fallback {
  align-items: center;
  background: #2563eb;
  color: white;
  display: flex;
  font-weight: 700;
  justify-content: center;
}

.sidebar__user-text {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.sidebar__user-text strong {
  color: white;
}

.sidebar__user-text span {
  color: #9ca3af;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
