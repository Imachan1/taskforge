# TaskForge

TaskForge is a full-stack project and task management MVP built with Laravel 12, Vue 3, Pinia, PrimeVue, Sanctum, Google OAuth, and SQLite. It is designed as a clean portfolio-grade application with separate frontend and backend deployments.

## Features

- Email/password registration and login
- Google OAuth login and account creation
- Sanctum bearer-token authentication
- Protected Vue routes with persisted auth state
- User profile and password management
- Project CRUD scoped to the authenticated user
- Task CRUD inside projects
- Dashboard statistics and recent activity
- Global search across the user's projects and tasks
- Feature tests for auth, OAuth, profile, projects, tasks, dashboard, and search

## Tech Stack

Frontend:

- Vue 3
- Vite
- Vue Router
- Pinia
- Axios
- PrimeVue

Backend:

- Laravel 12
- Laravel Sanctum
- Laravel Socialite
- SQLite
- PHPUnit

Deployment targets:

- Frontend: Vercel
- Backend: Railway

## Architecture

TaskForge uses a separated SPA/API architecture.

```text
Vue SPA on Vercel
        |
        | HTTPS JSON API + Bearer token
        v
Laravel API on Railway
        |
        v
SQLite database
```

Authentication is token based. Login, registration, and Google OAuth all create Sanctum personal access tokens. The frontend stores the token and current user in Pinia/localStorage, then sends the token through Axios as `Authorization: Bearer <token>`.

Ownership is enforced on the backend:

- A user owns many projects through `projects.owner_id`.
- A project has many tasks.
- Task authorization is checked through `task -> project -> owner_id`.

## Project Structure

```text
TaskForge/
  backend/        Laravel API
  frontend/       Vue SPA
  docs/           API, architecture, schema, roadmap, and requirements docs
  DEPLOYMENT.md   Vercel/Railway deployment guide
```

Important backend folders:

```text
backend/app/Http/Controllers
backend/app/Http/Requests
backend/app/Models
backend/database/migrations
backend/database/factories
backend/database/seeders
backend/tests/Feature
```

Important frontend folders:

```text
frontend/src/api
frontend/src/components
frontend/src/layouts
frontend/src/router
frontend/src/stores
frontend/src/views
```

## Local Setup

### Requirements

- PHP 8.2+
- Composer
- Node.js 20.19+ or 22.12+
- npm
- SQLite

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

Backend local URL:

```text
http://127.0.0.1:8000
```

### Frontend

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

Frontend local URL:

```text
http://localhost:5173
```

Frontend `.env` for local development:

```env
VITE_API_URL=http://127.0.0.1:8000/api
VITE_BACKEND_URL=http://127.0.0.1:8000
```

## Backend Environment Variables

Minimum local backend `.env` values:

```env
APP_NAME=TaskForge
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=sqlite

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax

SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,127.0.0.1,127.0.0.1:5173
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://127.0.0.1:5173

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

Generate `APP_KEY` with:

```bash
php artisan key:generate
```

## Google OAuth Setup

In Google Cloud Console:

1. Create or select a project.
2. Configure the OAuth consent screen.
3. Create OAuth 2.0 credentials for a web application.
4. Add authorized redirect URIs.

Local redirect URI:

```text
http://127.0.0.1:8000/auth/google/callback
```

Production redirect URI:

```text
https://<your-railway-backend-domain>/auth/google/callback
```

Set these backend variables:

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://<your-railway-backend-domain>/auth/google/callback
```

## Testing

Backend:

```bash
cd backend
vendor/bin/pint --dirty
php artisan test
```

Frontend:

```bash
cd frontend
npm run build
```

## Deployment

TaskForge is prepared for:

- Vercel frontend deployment
- Railway backend deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for full deployment instructions and production environment variables.

## API Endpoints

Public:

- `POST /api/register`
- `POST /api/login`
- `GET /auth/google/redirect`
- `GET /auth/google/callback`

Authenticated:

- `GET /api/me`
- `POST /api/logout`
- `GET /api/profile`
- `PUT /api/profile`
- `PUT /api/profile/password`
- `GET /api/dashboard`
- `GET /api/search?q=...`
- `GET /api/projects`
- `POST /api/projects`
- `GET /api/projects/{project}`
- `PUT/PATCH /api/projects/{project}`
- `DELETE /api/projects/{project}`
- `GET /api/projects/{project}/tasks`
- `POST /api/projects/{project}/tasks`
- `GET /api/tasks/{task}`
- `PUT/PATCH /api/tasks/{task}`
- `DELETE /api/tasks/{task}`

## Roadmap

- Production database migration from SQLite to PostgreSQL
- Password reset flow
- Email verification
- Project archive state
- Task filters and sorting
- Kanban board
- Comments
- File attachments
- Notifications
- Team/workspace collaboration
- CI pipeline for tests and frontend build
