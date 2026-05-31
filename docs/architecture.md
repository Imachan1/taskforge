# Architecture

TaskForge is a separated frontend/backend application.

```text
Vue 3 SPA
  - Vite
  - PrimeVue
  - Pinia
  - Vue Router
  - Axios

        |
        | HTTP JSON API
        | Authorization: Bearer <Sanctum token>
        v

Laravel 12 API
  - Sanctum
  - Socialite
  - Eloquent
  - SQLite
```

## Authentication

TaskForge uses token-based authentication.

- Email/password registration creates a user and Sanctum token.
- Email/password login creates a Sanctum token.
- Google OAuth finds or creates a user, then creates a Sanctum token.
- The frontend stores token/user in Pinia and localStorage.
- Axios attaches the token to every API request.
- Protected Vue routes validate persisted tokens through `/api/me`.

## Authorization Model

Data is scoped per user.

```text
User
  has many Projects

Project
  belongs to User through owner_id
  has many Tasks

Task
  belongs to Project
```

Project access is checked through `projects.owner_id`.

Task access is checked through `task.project.owner_id`.

## Frontend Modules

- Authentication: login, registration, Google callback
- Dashboard: statistics and recent projects/tasks
- Projects: CRUD and project details
- Tasks: CRUD inside `ProjectView`
- Search: global search across owned projects/tasks
- Profile: name/password management

## Backend Modules

- `AuthController`
- `GoogleAuthController`
- `ProfileController`
- `DashboardController`
- `ProjectController`
- `TaskController`
- `SearchController`

## Deployment

- Frontend: Vercel
- Backend: Railway
- Database: SQLite for MVP; PostgreSQL recommended for durable production data
