# TaskForge Deployment Guide

This guide prepares TaskForge for production deployment with minimal manual work.

## Chosen Hosting Strategy

For a free-tier pet project, the most realistic option is:

- Backend: Render Web Service (Docker)
- Frontend: Render Static Site
- Database: Free PostgreSQL (recommended: Neon)

Why this option:

- Works with current monorepo structure.
- `render.yaml` supports one-click blueprint deployment.
- Laravel + Socialite + Sanctum are straightforward in this setup.
- Free Render web/static is available for small personal projects.

## Included Deployment Artifacts

The repository now contains:

- `render.yaml` (Render blueprint for backend + frontend)
- `backend/Dockerfile` (production backend image)
- `backend/.dockerignore`
- `backend/scripts/start-render.sh` (startup script with migrate)

## Production Architecture

```text
Render Static Site (Vue)
        |
        | HTTPS API + Bearer token
        v
Render Docker Web Service (Laravel)
        |
        v
Neon PostgreSQL (free)
```

## Why Not SQLite in Free Production

SQLite works locally and for prototyping, but on free web instances it is risky because local filesystem can be ephemeral across redeploy/restarts.

For stable production data, use PostgreSQL in production.

SQLite is still supported for local development and can be used in production only if a persistent disk is guaranteed.

## Backend (Render Web Service)

Render uses:

- `backend/Dockerfile`
- startup script `backend/scripts/start-render.sh`

Startup script behavior:

1. validates `APP_KEY`
2. ensures SQLite file exists (if SQLite selected)
3. runs `php artisan migrate --force`
4. runs `php artisan storage:link` (non-fatal)
5. starts server on `${PORT}`

## Frontend (Render Static Site)

Render uses:

- root directory: `frontend`
- build command: `npm ci && npm run build`
- publish directory: `dist`
- SPA rewrite route to `/index.html` is included in `render.yaml`

## Required Environment Variables

### Backend required

```env
APP_NAME=TaskForge
APP_ENV=production
APP_DEBUG=false
APP_URL=https://<backend-domain>.onrender.com
FRONTEND_URL=https://<frontend-domain>.onrender.com
APP_KEY=base64:...

DB_CONNECTION=pgsql
DB_URL=postgresql://<user>:<password>@<host>/<db>?sslmode=require

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
QUEUE_CONNECTION=sync

SANCTUM_STATEFUL_DOMAINS=<frontend-domain>.onrender.com
CORS_ALLOWED_ORIGINS=https://<frontend-domain>.onrender.com

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://<backend-domain>.onrender.com/auth/google/callback
```

Notes:

- `SANCTUM_STATEFUL_DOMAINS` must contain host only (no protocol/path).
- `CORS_ALLOWED_ORIGINS` must contain full origin(s).
- For multiple frontend origins, separate by comma.

### Frontend required

```env
VITE_API_URL=https://<backend-domain>.onrender.com/api
VITE_BACKEND_URL=https://<backend-domain>.onrender.com
```

## Google OAuth Production Setup

In Google Cloud Console (OAuth client):

Authorized redirect URI:

```text
https://<backend-domain>.onrender.com/auth/google/callback
```

Flow:

1. Frontend opens `/auth/google/redirect` on backend.
2. Google redirects to backend callback.
3. Backend creates Sanctum token and redirects back to frontend callback route.

## Sanctum, Sessions, Cache, Storage, Migrations

Current production-safe choices for this project:

- Sanctum: bearer tokens (SPA cookie mode is not required)
- Sessions: `file` driver (no sessions table dependency)
- Cache: `file` driver (no extra infrastructure required)
- Storage: local disk is enough for current feature set
- Migrations: executed automatically on backend startup (`migrate --force`)

## Step-by-Step Publish (Render)

1. Push repository to GitHub.
2. In Render, choose New > Blueprint and select this repository.
3. Render detects `render.yaml` and proposes two services.
4. Create a free PostgreSQL database at Neon.
5. Fill backend env vars in Render:
   - `APP_URL`
   - `FRONTEND_URL`
   - `APP_KEY`
   - `DB_URL`
   - `SANCTUM_STATEFUL_DOMAINS`
   - `CORS_ALLOWED_ORIGINS`
   - `GOOGLE_CLIENT_ID`
   - `GOOGLE_CLIENT_SECRET`
   - `GOOGLE_REDIRECT_URI`
6. Fill frontend env vars in Render:
   - `VITE_API_URL`
   - `VITE_BACKEND_URL`
7. Deploy backend and frontend.
8. Update Google OAuth redirect URI to the real backend domain.
9. Redeploy backend once after OAuth env update.

## Smoke Test After Deploy

1. Backend health:

```bash
curl https://<backend-domain>.onrender.com/api/test
```

Expected:

```json
{"message":"API works"}
```

2. Frontend:
   - open frontend URL
   - register/login by email-password
   - login by Google OAuth
   - create project and task
   - verify dashboard and search
   - open profile and logout

## Local/CI Production Build Checks

Backend tests:

```bash
cd backend
php artisan test
```

Frontend production build:

```bash
cd frontend
npm run build
```

## Troubleshooting

### CORS error

- verify `CORS_ALLOWED_ORIGINS` contains exact frontend origin
- run backend redeploy after env changes

### OAuth returns to wrong URL

- verify `FRONTEND_URL`
- verify `GOOGLE_REDIRECT_URI`
- verify Google Console redirect URI exactly matches backend callback

### 401 after login

- verify frontend uses correct `VITE_API_URL`
- verify token is sent as `Authorization: Bearer ...`

### Database connection failure

- verify `DB_CONNECTION=pgsql`
- verify `DB_URL` includes ssl mode (`sslmode=require`)
