# Database Schema

TaskForge currently uses SQLite.

## Users

| Field             | Type      | Notes                         |
| ----------------- | --------- | ----------------------------- |
| id                | integer   | Primary key                   |
| name              | string    | Display name                  |
| email             | string    | Unique                        |
| email_verified_at | timestamp | Nullable                      |
| password          | string    | Hashed                        |
| google_id         | string    | Nullable, unique              |
| avatar_url        | string    | Nullable                      |
| remember_token    | string    | Nullable                      |
| created_at        | timestamp |                               |
| updated_at        | timestamp |                               |

## Personal Access Tokens

Sanctum token table.

| Field          | Type      |
| -------------- | --------- |
| id             | integer   |
| tokenable_type | string    |
| tokenable_id   | integer   |
| name           | text      |
| token          | string    |
| abilities      | text      |
| last_used_at   | timestamp |
| expires_at     | timestamp |
| created_at     | timestamp |
| updated_at     | timestamp |

## Projects

| Field       | Type      | Notes                 |
| ----------- | --------- | --------------------- |
| id          | integer   | Primary key           |
| name        | string    | Required              |
| description | text      | Nullable              |
| owner_id    | integer   | Foreign key: users.id |
| created_at  | timestamp |                       |
| updated_at  | timestamp |                       |

## Tasks

| Field       | Type      | Notes                     |
| ----------- | --------- | ------------------------- |
| id          | integer   | Primary key               |
| project_id  | integer   | Foreign key: projects.id  |
| title       | string    | Required                  |
| description | text      | Nullable                  |
| status      | string    | todo/in_progress/done     |
| priority    | string    | low/medium/high           |
| due_date    | date      | Nullable                  |
| created_at  | timestamp |                           |
| updated_at  | timestamp |                           |

## Sessions, Cache, Jobs

Laravel default tables are included for database-backed sessions, cache, and queues.

## Relationships

```text
User 1 -> many Projects
Project 1 -> many Tasks
Task many -> 1 Project
Project many -> 1 User
```

## Future Schema Ideas

- Workspaces/teams
- Project members
- Comments
- File attachments
- Notifications
- Password reset tokens UI flow
