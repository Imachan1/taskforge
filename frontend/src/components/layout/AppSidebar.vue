<template>
  <aside class="sidebar">
    <div class="sidebar__logo">
      TaskForge
    </div>

    <nav class="sidebar__nav">
      <RouterLink to="/dashboard">Dashboard</RouterLink>

      <RouterLink to="/projects">Projects</RouterLink>

      <RouterLink to="/search">Search</RouterLink>
    </nav>
  </aside>
</template>

<style scoped lang="scss">
.sidebar {
  width: 240px;
  min-height: 100vh;
  position: relative;
  background:
    linear-gradient(170deg, rgba(7, 22, 44, 0.82), rgba(5, 17, 34, 0.72)),
    rgba(4, 15, 30, 0.6);
  border-right: 1px solid rgba(125, 211, 252, 0.16);
  box-shadow:
    inset -1px 0 0 rgba(56, 189, 248, 0.06),
    10px 0 40px rgba(2, 6, 23, 0.32);
  backdrop-filter: blur(12px) saturate(118%);
  -webkit-backdrop-filter: blur(12px) saturate(118%);
  display: flex;
  flex-direction: column;
  gap: 24px;
  padding: 24px;
}

.sidebar::before {
  content: '';
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    radial-gradient(circle at 16% 8%, rgba(56, 189, 248, 0.12), transparent 34%),
    linear-gradient(180deg, rgba(59, 130, 246, 0.06), transparent 28%);
}

.sidebar__logo {
  color: #f8fbff;
  font-size: 24px;
  font-weight: 700;
  letter-spacing: 0.01em;
  margin-bottom: 32px;
}

.sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
}

.sidebar__nav a {
  color: #b7d2ec;
  text-decoration: none;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1px solid transparent;
  transition: 180ms;
}

.sidebar__nav a:hover {
  color: #e7f4ff;
  border-color: rgba(125, 211, 252, 0.22);
  background: rgba(17, 58, 126, 0.28);
}

.sidebar__nav a.router-link-active {
  color: #ffffff;
  border-color: rgba(125, 211, 252, 0.2);
  background: linear-gradient(135deg, rgba(29, 78, 216, 0.86), rgba(37, 99, 235, 0.8));
  box-shadow:
    0 8px 20px rgba(30, 64, 175, 0.32),
    0 0 14px rgba(56, 189, 248, 0.16);
}
</style>
