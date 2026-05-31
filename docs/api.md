# API Reference

Base path for API endpoints:

```text
/api
```

Authentication uses Sanctum bearer tokens:

```http
Authorization: Bearer <token>
Accept: application/json
```

## Public Endpoints

### Register

```http
POST /api/register
```

Body:

```json
{
  "name": "Valeriia",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login

```http
POST /api/login
```

Body:

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

Response:

```json
{
  "message": "Logged in successfully",
  "token": "1|...",
  "token_type": "Bearer",
  "user": {}
}
```

### Google OAuth

```http
GET /auth/google/redirect
GET /auth/google/callback
```

## Authenticated Endpoints

### Current User

```http
GET /api/me
```

### Logout

```http
POST /api/logout
```

### Profile

```http
GET /api/profile
PUT /api/profile
PUT /api/profile/password
```

`PUT /api/profile` body:

```json
{
  "name": "Updated Name"
}
```

`PUT /api/profile/password` body:

```json
{
  "current_password": "password123",
  "password": "new-password123",
  "password_confirmation": "new-password123"
}
```

### Dashboard

```http
GET /api/dashboard
```

Response:

```json
{
  "projects_count": 3,
  "tasks_count": 9,
  "todo_count": 3,
  "in_progress_count": 3,
  "done_count": 3,
  "completion_rate": 33,
  "recent_projects": [],
  "recent_tasks": []
}
```

### Search

```http
GET /api/search?q=launch
```

Response:

```json
{
  "projects": [],
  "tasks": [
    {
      "id": 1,
      "project_id": 1,
      "project_name": "TaskForge Launch",
      "title": "Define scope"
    }
  ]
}
```

### Projects

```http
GET /api/projects
POST /api/projects
GET /api/projects/{project}
PUT /api/projects/{project}
PATCH /api/projects/{project}
DELETE /api/projects/{project}
```

Create/update body:

```json
{
  "name": "TaskForge Launch",
  "description": "Initial MVP delivery"
}
```

### Tasks

List and create tasks inside a project:

```http
GET /api/projects/{project}/tasks
POST /api/projects/{project}/tasks
```

Read, update, and delete tasks:

```http
GET /api/tasks/{task}
PUT /api/tasks/{task}
PATCH /api/tasks/{task}
DELETE /api/tasks/{task}
```

Create/update body:

```json
{
  "title": "Implement profile page",
  "description": "Build profile view and API integration",
  "status": "todo",
  "priority": "medium",
  "due_date": "2026-06-30"
}
```

Allowed task statuses:

- `todo`
- `in_progress`
- `done`

Allowed priorities:

- `low`
- `medium`
- `high`

## Error Codes

- `200 OK`
- `201 Created`
- `401 Unauthorized`
- `404 Not Found`
- `422 Validation Error`
- `500 Internal Server Error`
