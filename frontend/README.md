# TaskForge Frontend

Vue 3 SPA for TaskForge.

## Stack

- Vue 3
- Vite
- PrimeVue
- Pinia
- Vue Router
- Axios

## Local Setup

```bash
npm install
cp .env.example .env
npm run dev
```

Required `.env`:

```env
VITE_API_URL=http://127.0.0.1:8000/api
VITE_BACKEND_URL=http://127.0.0.1:8000
```

## Build

```bash
npm run build
```

## Deployment

The frontend is prepared for Vercel. See the root [DEPLOYMENT.md](../DEPLOYMENT.md).
