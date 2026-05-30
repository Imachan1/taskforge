<script setup>
import AppSidebar from '../components/layout/AppSidebar.vue'
</script>

<template>
  <div class="layout">
    <AppSidebar />

    <main class="content">
      <router-view />
    </main>
  </div>
</template>

<style scoped lang="scss">
.layout {
  display: flex;
}

.content {
  flex: 1;

  padding: 32px;

  min-height: 100vh;

  background: #0f172a;
}
</style>