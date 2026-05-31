# Requirements

## Product Scope

TaskForge is an MVP project and task management application for a single authenticated user workspace.

## Functional Requirements

Authentication:

- Users can register with name, email, and password.
- Users can log in with email/password.
- Users can log in or register through Google OAuth.
- Users can log out.
- Users can view and update their profile.
- Users can change their password.

Projects:

- Users can create projects.
- Users can list only their own projects.
- Users can view, update, and delete only their own projects.

Tasks:

- Tasks belong to projects.
- Users can list, create, view, update, and delete tasks only within their own projects.
- Task statuses: `todo`, `in_progress`, `done`.
- Task priorities: `low`, `medium`, `high`.

Dashboard:

- Users can view project/task counts.
- Users can view task status counts.
- Users can view completion rate.
- Users can view recent projects and tasks.

Search:

- Users can search their own projects by name and description.
- Users can search tasks in their own projects by title and description.

## Non-Functional Requirements

- All user data access must be scoped to the authenticated user.
- API responses must be JSON.
- Protected endpoints must require Sanctum bearer-token authentication.
- Frontend configuration must use environment variables for backend URLs.
- Backend CORS must be environment-driven for Vercel/Railway deployment.
- Automated backend tests must pass before deployment.
- Frontend production build must pass before deployment.

## Out of Scope for Current MVP

- Multi-user teams
- Project members
- Comments
- File attachments
- Notifications
- News feed
- Elasticsearch
- Docker Compose production stack
