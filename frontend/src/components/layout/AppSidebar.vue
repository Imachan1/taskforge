<template>
  <aside class="sidebar">
    <div class="sidebar__logo">
      TaskForge
    </div>

    <nav class="sidebar__nav">
      <RouterLink to="/dashboard">Dashboard</RouterLink>

      <RouterLink to="/projects">Projects</RouterLink>

      <RouterLink to="/tasks">Tasks</RouterLink>

      <RouterLink to="/news">News</RouterLink>

      <RouterLink to="/search">Search</RouterLink>
    </nav>
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
</style>