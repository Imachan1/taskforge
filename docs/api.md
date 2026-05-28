# API Specification

## Overview

TaskForge uses a REST API architecture.

Frontend (Vue.js) communicates with Backend (Laravel) through HTTP requests.

Authentication is handled using Laravel Sanctum.

Base URL:

```text
/api
```

---

# Authentication

## Login

Authenticate user.

### Request

```http
POST /api/login
```

### Body

```json
{
  "email": "user@example.com",
  "password": "password"
}
```

### Response

```json
{
  "user": {},
  "token": "..."
}
```

---

## Logout

Logout current user.

### Request

```http
POST /api/logout
```

---

## Current User

Get authenticated user.

### Request

```http
GET /api/user
```

---

# Projects

## Get Projects

Returns all available projects.

### Request

```http
GET /api/projects
```

---

## Get Project

Returns a single project.

### Request

```http
GET /api/projects/{id}
```

---

## Create Project

Creates a new project.

### Request

```http
POST /api/projects
```

### Body

```json
{
  "name": "TaskForge MVP",
  "description": "Main development project"
}
```

---

## Update Project

Updates project information.

### Request

```http
PUT /api/projects/{id}
```

---

## Delete Project

Archives or deletes project.

### Request

```http
DELETE /api/projects/{id}
```

---

# Project Members

## Get Project Members

Returns project members.

### Request

```http
GET /api/projects/{id}/members
```

---

## Add Member

Adds user to project.

### Request

```http
POST /api/projects/{id}/members
```

### Body

```json
{
  "user_id": 5
}
```

---

## Remove Member

Removes user from project.

### Request

```http
DELETE /api/projects/{id}/members/{userId}
```

---

# Tasks

## Get Tasks

Returns task list.

### Request

```http
GET /api/tasks
```

---

## Get Task

Returns task details.

### Request

```http
GET /api/tasks/{id}
```

---

## Create Task

Creates task.

### Request

```http
POST /api/tasks
```

### Body

```json
{
  "project_id": 1,
  "title": "Implement login page",
  "description": "Create login UI",
  "priority": "high",
  "assignee_id": 3,
  "deadline": "2026-06-15"
}
```

---

## Update Task

Updates task.

### Request

```http
PUT /api/tasks/{id}
```

---

## Delete Task

Deletes task.

### Request

```http
DELETE /api/tasks/{id}
```

---

## Change Task Status

Moves task between workflow stages.

### Request

```http
PATCH /api/tasks/{id}/status
```

### Body

```json
{
  "status_id": 2
}
```

---

# Task Statuses

## Get Statuses

Returns workflow statuses.

### Request

```http
GET /api/task-statuses
```

---

# Comments

## Get Task Comments

Returns comments for a task.

### Request

```http
GET /api/tasks/{id}/comments
```

---

## Create Comment

Adds comment.

### Request

```http
POST /api/tasks/{id}/comments
```

### Body

```json
{
  "content": "Login page completed."
}
```

---

## Update Comment

Updates existing comment.

### Request

```http
PUT /api/comments/{id}
```

---

## Delete Comment

Deletes comment.

### Request

```http
DELETE /api/comments/{id}
```

---

# Attachments

## Get Task Attachments

Returns uploaded files.

### Request

```http
GET /api/tasks/{id}/attachments
```

---

## Upload Attachment

Uploads file.

### Request

```http
POST /api/tasks/{id}/attachments
```

### Content Type

```text
multipart/form-data
```

---

## Download Attachment

Downloads file.

### Request

```http
GET /api/attachments/{id}
```

---

## Delete Attachment

Deletes file.

### Request

```http
DELETE /api/attachments/{id}
```

---

# News

## Get News Feed

Returns announcements.

### Request

```http
GET /api/news
```

---

## Get News Item

Returns a single announcement.

### Request

```http
GET /api/news/{id}
```

---

## Create News

Creates announcement.

### Request

```http
POST /api/news
```

### Body

```json
{
  "title": "Version 1 Released",
  "content": "TaskForge MVP is available."
}
```

---

## Update News

Updates announcement.

### Request

```http
PUT /api/news/{id}
```

---

## Delete News

Deletes announcement.

### Request

```http
DELETE /api/news/{id}
```

---

# Activity Log

## Get Activity Feed

Returns latest activity.

### Request

```http
GET /api/activity
```

---

## Get Project Activity

Returns activity for a specific project.

### Request

```http
GET /api/projects/{id}/activity
```

---

# Search

## Global Search

Searches across projects, tasks, comments, files and news.

### Request

```http
GET /api/search?q=login
```

### Example Response

```json
{
  "projects": [],
  "tasks": [],
  "comments": [],
  "attachments": [],
  "news": []
}
```

---

# Dashboard

## Dashboard Summary

Returns dashboard statistics.

### Request

```http
GET /api/dashboard
```

### Example Response

```json
{
  "active_projects": 5,
  "assigned_tasks": 12,
  "overdue_tasks": 2,
  "recent_news": []
}
```

---

# Response Codes

## Success

```text
200 OK
201 Created
204 No Content
```

## Client Errors

```text
400 Bad Request
401 Unauthorized
403 Forbidden
404 Not Found
422 Validation Error
```

## Server Errors

```text
500 Internal Server Error
```
