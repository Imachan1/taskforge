<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import AppSidebar from '../components/layout/AppSidebar.vue'
import { useAuthStore } from '../stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const routeTitles = {
  dashboard: 'Dashboard',
  projects: 'Projects',
  project: 'Project',
  search: 'Search',
  profile: 'Profile',
}

const currentTitle = computed(() => routeTitles[route.name] || 'Workspace')

const initials = computed(() => {
  const name = auth.user?.name || auth.user?.email || 'U'
  return name
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase()
})

const openProfile = () => {
  router.push({ name: 'profile' })
}

const logout = async () => {
  await auth.logout()
  await router.push({ name: 'login' })
}
</script>

<template>
  <div class="layout">
    <AppSidebar />

    <main class="content">
      <header class="topbar">
        <div class="topbar__title-block">
          <p class="topbar__eyebrow">Workspace</p>
          <h1>{{ currentTitle }}</h1>
        </div>

        <div class="topbar__actions">
          <button
            class="topbar__profile-btn"
            type="button"
            @click="openProfile"
          >
            <img
              v-if="auth.user?.avatar_url"
              :src="auth.user.avatar_url"
              alt=""
              class="topbar__avatar"
            >
            <span
              v-else
              class="topbar__avatar topbar__avatar--fallback"
            >
              {{ initials }}
            </span>
            <span class="topbar__profile-text">{{ auth.user?.name || 'Profile' }}</span>
          </button>

          <button
            class="topbar__logout-btn"
            type="button"
            @click="logout"
          >
            Logout
          </button>
        </div>
      </header>

      <section class="content-stage">
        <router-view v-slot="{ Component, route: viewRoute }">
          <transition
            name="route-fade"
            mode="out-in"
          >
            <component
              :is="Component"
              :key="viewRoute.fullPath"
            />
          </transition>
        </router-view>
      </section>
    </main>
  </div>
</template>

<style scoped lang="scss">
.layout {
  position: relative;
  isolation: isolate;
  display: flex;
  min-height: 100vh;
  background:
    radial-gradient(circle at 50% 0%, rgba(27, 120, 255, 0.16), transparent 34rem),
    linear-gradient(135deg, #020817 0%, #07162c 48%, #020817 100%);
}

.layout::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: -2;
  background-image:
    linear-gradient(rgba(96, 165, 250, 0.045) 1px, transparent 1px),
    linear-gradient(90deg, rgba(96, 165, 250, 0.045) 1px, transparent 1px);
  background-size: 48px 48px;
  mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.88), transparent 88%);
}

.layout::after {
  content: '';
  position: absolute;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  background:
    radial-gradient(circle at 15% 20%, rgba(56, 189, 248, 0.1), transparent 30%),
    radial-gradient(circle at 88% 12%, rgba(59, 130, 246, 0.11), transparent 36%);
}

.content {
  flex: 1;
  position: relative;
  padding: 1rem 1.25rem 1.25rem;
  min-height: 100vh;
  color: #dcecff;
  background: transparent;
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 0.8rem 1rem;
  border: 1px solid rgba(125, 211, 252, 0.16);
  border-radius: 16px;
  background:
    linear-gradient(145deg, rgba(8, 23, 47, 0.58), rgba(15, 42, 76, 0.28)),
    rgba(7, 18, 36, 0.16);
  box-shadow:
    0 16px 34px rgba(0, 0, 0, 0.2),
    0 0 0 1px rgba(56, 189, 248, 0.05) inset;
  backdrop-filter: blur(10px) saturate(118%);
  -webkit-backdrop-filter: blur(10px) saturate(118%);
}

.topbar__title-block h1 {
  margin: 0;
  font-size: 1.35rem;
}

.topbar__eyebrow {
  margin: 0 0 0.16rem;
  color: #67e8f9;
  font-size: 0.74rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.topbar__actions {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.topbar__profile-btn,
.topbar__logout-btn {
  border: 1px solid rgba(125, 211, 252, 0.2);
  border-radius: 12px;
  background: rgba(8, 23, 47, 0.48);
  color: #e8f5ff;
  cursor: pointer;
  transition: 180ms;
}

.topbar__profile-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.45rem 0.75rem;
}

.topbar__logout-btn {
  padding: 0.45rem 0.78rem;
  font-weight: 600;
}

.topbar__profile-btn:hover,
.topbar__logout-btn:hover {
  border-color: rgba(125, 211, 252, 0.34);
  background: rgba(17, 58, 126, 0.34);
}

.topbar__avatar {
  width: 30px;
  height: 30px;
  border-radius: 999px;
  object-fit: cover;
}

.topbar__avatar--fallback {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0ea5e9, #2563eb);
  color: #ffffff;
  font-size: 0.74rem;
  font-weight: 700;
}

.topbar__profile-text {
  max-width: 11rem;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.content-stage {
  min-height: calc(100vh - 5.75rem);
}

.route-fade-enter-active,
.route-fade-leave-active {
  transition:
    opacity 210ms ease,
    transform 210ms ease;
}

.route-fade-enter-from,
.route-fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

.content :deep(h1),
.content :deep(h2),
.content :deep(h3),
.content :deep(strong),
.content :deep(label) {
  color: #f0f8ff;
}

.content :deep(.p-card) {
  border: 1px solid rgba(125, 211, 252, 0.16);
  border-radius: 20px;
  background:
    linear-gradient(145deg, rgba(8, 23, 47, 0.64), rgba(15, 42, 76, 0.34)),
    rgba(7, 18, 36, 0.2);
  box-shadow:
    0 20px 52px rgba(0, 0, 0, 0.24),
    0 0 0 1px rgba(56, 189, 248, 0.05) inset;
  backdrop-filter: blur(10px) saturate(118%);
  -webkit-backdrop-filter: blur(10px) saturate(118%);
}

.content :deep(.p-card .p-card-title) {
  color: #eef7ff;
}

.content :deep(.p-card .p-card-content),
.content :deep(.p-card .p-card-body),
.content :deep(.p-card .p-card-subtitle) {
  color: #b6d0ea;
}

.content :deep(.p-button) {
  border-radius: 14px;
}

.content :deep(.p-button:not(.p-button-text):not(.p-button-outlined)) {
  border: 1px solid rgba(125, 211, 252, 0.3);
  background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 58%, #1d4ed8 100%);
  box-shadow: 0 12px 26px rgba(37, 99, 235, 0.28);
}

.content :deep(.p-button.p-button-text) {
  color: #b6d0ea;
}

.content :deep(.p-datatable) {
  border: 1px solid rgba(125, 211, 252, 0.14);
  border-radius: 16px;
  overflow: hidden;
}

.content :deep(.p-datatable-table-container),
.content :deep(.p-datatable-table),
.content :deep(.p-datatable-header),
.content :deep(.p-datatable-thead > tr > th),
.content :deep(.p-datatable-tbody > tr > td),
.content :deep(.p-datatable-tbody > tr) {
  background: rgba(6, 20, 40, 0.56) !important;
  color: #d4e6f8;
  border-color: rgba(125, 211, 252, 0.12) !important;
}

.content :deep(.p-datatable-thead > tr > th) {
  color: #a9c6e8;
  font-weight: 700;
}

.content :deep(.p-datatable-tbody > tr:hover > td) {
  background: rgba(12, 36, 66, 0.74) !important;
}

.content :deep(.p-dialog .p-dialog-header),
.content :deep(.p-dialog .p-dialog-content),
.content :deep(.p-dialog .p-dialog-footer) {
  background: rgba(5, 17, 34, 0.92);
  color: #dcecff;
  border-color: rgba(125, 211, 252, 0.12);
}

.content :deep(.p-inputtext),
.content :deep(.p-textarea),
.content :deep(.p-password-input),
.content :deep(.p-select) {
  border: 1px solid rgba(148, 197, 255, 0.24);
  border-radius: 14px;
  background: rgba(4, 16, 33, 0.56);
  color: #eaf6ff;
}

.content :deep(.p-inputtext::placeholder),
.content :deep(.p-textarea::placeholder),
.content :deep(.p-password-input::placeholder) {
  color: #86a9cb;
}

.content :deep(.p-inputtext:focus),
.content :deep(.p-textarea:focus),
.content :deep(.p-password-input:focus),
.content :deep(.p-select.p-focus) {
  border-color: rgba(56, 189, 248, 0.88);
  box-shadow:
    0 0 0 3px rgba(14, 165, 233, 0.14),
    0 0 22px rgba(56, 189, 248, 0.15);
}

@media (max-width: 900px) {
  .content {
    padding: 0.75rem;
  }

  .topbar {
    flex-direction: column;
    align-items: flex-start;
  }

  .topbar__actions {
    width: 100%;
    justify-content: space-between;
  }

  .topbar__profile-text {
    max-width: 8rem;
  }
}
</style>