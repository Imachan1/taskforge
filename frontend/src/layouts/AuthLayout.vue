<template>
  <div class="auth-layout">
    <div class="auth-card">
      <RouterView />
    </div>
  </div>
</template>

<style scoped>
.auth-layout {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;

  background: #0f172a;
}

.auth-card {
  width: 100%;
  max-width: 450px;

  padding: 2rem;

  background: #1e293b;
  border-radius: 12px;

  box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
}
</style>