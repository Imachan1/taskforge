# TaskForge Deployment Guide

This guide describes how to deploy TaskForge with:

- Frontend on Vercel
- Backend on Railway
- Google OAuth through Laravel Socialite
- Sanctum bearer tokens

## Production Architecture

```text
Vercel Vue SPA
    |
    | VITE_API_URL
    v
Railway Laravel API
    |
    v
SQLite database
```

The application currently uses SQLite. For a durable production setup on Railway, attach a persistent volume or migrate to PostgreSQL before relying on production data.

## Backend on Railway

### Railway Project Setup

1. Create a new Railway project.
2. Connect the Git repository.
3. Set the service root directory to `backend`.
4. Railway can use `backend/Procfile`:

```text
web: php artisan serve --host=0.0.0.0 --port=$PORT
```

5. Add all required environment variables.
6. Run migrations from Railway shell or deployment command:

```bash
php artisan migrate --force
```

Optional seed command:

```bash
php artisan db:seed --force
```

### Backend Production Environment Variables

Required:

```env
APP_NAME=TaskForge
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://<your-railway-backend-domain>
FRONTEND_URL=https://<your-vercel-frontend-domain>

DB_CONNECTION=sqlite

SESSION_DRIVER=database
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

SANCTUM_STATEFUL_DOMAINS=<your-vercel-frontend-domain>
CORS_ALLOWED_ORIGINS=https://<your-vercel-frontend-domain>

GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://<your-railway-backend-domain>/auth/google/callback
```

Recommended:

```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

### APP_KEY

Generate locally or in Railway shell:

```bash
php artisan key:generate --show
```

Copy the generated value into Railway as `APP_KEY`.

### CORS

`CORS_ALLOWED_ORIGINS` must include the Vercel frontend origin:

```env
CORS_ALLOWED_ORIGINS=https://taskforge.example.vercel.app
```

Do not include paths. Use origins only.

### Sanctum

TaskForge uses Sanctum personal access tokens as bearer tokens. Cookie-based SPA auth is not required for the current frontend. Keep `SANCTUM_STATEFUL_DOMAINS` aligned with the frontend domain for future compatibility:

```env
SANCTUM_STATEFUL_DOMAINS=taskforge.example.vercel.app
```

## Frontend on Vercel

### Vercel Project Setup

1. Create a Vercel project.
2. Connect the Git repository.
3. Set the root directory to `frontend`.
4. Build command:

```bash
npm run build
```

5. Output directory:

```text
dist
```

### Frontend Production Environment Variables

Required:

```env
VITE_API_URL=https://<your-railway-backend-domain>/api
VITE_BACKEND_URL=https://<your-railway-backend-domain>
```

`VITE_API_URL` is used by Axios for API calls.

`VITE_BACKEND_URL` is used to start the Google OAuth redirect flow.

## Google OAuth

In Google Cloud Console, add these authorized redirect URIs.

Local:

```text
http://127.0.0.1:8000/auth/google/callback
```

Production:

```text
https://<your-railway-backend-domain>/auth/google/callback
```

The OAuth flow is:

1. User clicks `Continue with Google`.
2. Vue redirects to `GET /auth/google/redirect` on Railway.
3. Laravel Socialite redirects to Google.
4. Google redirects to `GET /auth/google/callback` on Railway.
5. Laravel finds or creates the user.
6. Laravel creates a Sanctum token.
7. Laravel redirects to `FRONTEND_URL/auth/google/callback?token=...&user=...`.
8. Vue stores the token/user and opens Dashboard.

## Production Checklist

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` is the Railway backend URL
- `FRONTEND_URL` is the Vercel frontend URL
- `CORS_ALLOWED_ORIGINS` contains the Vercel frontend origin
- `GOOGLE_REDIRECT_URI` matches the Railway callback URL
- Google Cloud Console contains the production callback URL
- Vercel has `VITE_API_URL` and `VITE_BACKEND_URL`
- Railway has a durable database plan or persistent volume
- `php artisan migrate --force` has been run
- `npm run build` passes locally before deploying

## Smoke Tests After Deployment

Backend:

```bash
curl https://<backend>/api/test
```

Frontend:

1. Open the Vercel URL.
2. Register a user.
3. Log out.
4. Log in with email/password.
5. Create a project.
6. Create a task inside the project.
7. Open Dashboard.
8. Search for the project/task.
9. Test Google login.
