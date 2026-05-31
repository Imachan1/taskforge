<template>
  <div class="auth-layout">
    <div class="auth-orb auth-orb--one" />
    <div class="auth-orb auth-orb--two" />
    <div class="auth-orb auth-orb--three" />
    <div class="auth-wave auth-wave--one" />
    <div class="auth-wave auth-wave--two" />

    <RouterView />
  </div>
</template>

<style scoped>
.auth-layout {
  position: relative;
  isolation: isolate;
  overflow: hidden;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 2rem;
  background:
    radial-gradient(circle at 50% 0%, rgba(27, 120, 255, 0.18), transparent 34rem),
    linear-gradient(135deg, #020817 0%, #07162c 48%, #020817 100%);
}

.auth-layout::before {
  content: '';
  position: absolute;
  inset: 0;
  z-index: -3;
  background-image:
    linear-gradient(rgba(96, 165, 250, 0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(96, 165, 250, 0.05) 1px, transparent 1px);
  background-size: 48px 48px;
  mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.85), transparent 82%);
}

.auth-orb,
.auth-wave {
  position: absolute;
  z-index: -2;
  pointer-events: none;
}

.auth-orb {
  border-radius: 999px;
  filter: blur(18px);
  opacity: 0.7;
  animation: drift 18s ease-in-out infinite alternate;
}

.auth-orb--one {
  width: 22rem;
  height: 22rem;
  top: -7rem;
  left: -4rem;
  background: radial-gradient(circle, rgba(0, 174, 255, 0.38), transparent 68%);
}

.auth-orb--two {
  width: 30rem;
  height: 30rem;
  right: -10rem;
  bottom: -12rem;
  background: radial-gradient(circle, rgba(37, 99, 235, 0.42), transparent 70%);
  animation-delay: -8s;
}

.auth-orb--three {
  width: 16rem;
  height: 16rem;
  right: 18%;
  top: 16%;
  background: radial-gradient(circle, rgba(56, 189, 248, 0.24), transparent 72%);
  animation-delay: -4s;
}

.auth-wave {
  width: 68rem;
  height: 18rem;
  border: 1px solid rgba(56, 189, 248, 0.18);
  border-radius: 50%;
  transform: rotate(-12deg);
  opacity: 0.5;
  animation: wave 16s ease-in-out infinite alternate;
}

.auth-wave--one {
  left: -16rem;
  bottom: 8%;
}

.auth-wave--two {
  right: -22rem;
  top: 12%;
  animation-delay: -6s;
}

@keyframes drift {
  from {
    transform: translate3d(0, 0, 0) scale(1);
  }

  to {
    transform: translate3d(2.4rem, -1.6rem, 0) scale(1.08);
  }
}

@keyframes wave {
  from {
    transform: translate3d(0, 0, 0) rotate(-12deg);
  }

  to {
    transform: translate3d(2rem, -1rem, 0) rotate(-8deg);
  }
}

@media (max-width: 640px) {
  .auth-layout {
    align-items: stretch;
    padding: 1rem;
  }

  .auth-orb--one {
    left: -10rem;
  }
}

@media (min-width: 641px) and (max-width: 1100px) {
  .auth-layout {
    padding: 1.25rem 2rem;
  }
}
</style>
