# Database Schema

## Overview

TaskForge uses a relational database structure based on MySQL.

The schema is designed to support:

* User authentication
* Project management
* Task tracking
* Workflow management
* Comments
* File attachments
* Internal news
* Activity logging
* Global search indexing

---

# Entity Relationship Overview

User
│
├── Project (created_by)
│
├── Task (assigned_to)
│
├── Task (reported_by)
│
├── Comment
│
├── Attachment
│
├── News
│
└── ActivityLog

Project
│
├── ProjectMember
│
└── Task

Task
│
├── Comment
│
├── Attachment
│
└── TaskStatus

---

# Tables

## Users

Stores system users.

| Field      | Type      |
| ---------- | --------- |
| id         | bigint    |
| name       | string    |
| email      | string    |
| password   | string    |
| role       | string    |
| created_at | timestamp |
| updated_at | timestamp |

### Roles

* admin
* manager
* member

---

## Projects

Stores projects.

| Field       | Type                    |
| ----------- | ----------------------- |
| id          | bigint                  |
| name        | string                  |
| description | text                    |
| status      | string                  |
| created_by  | foreign key -> users.id |
| created_at  | timestamp               |
| updated_at  | timestamp               |

### Statuses

* active
* completed
* archived

---

## Project Members

Connects users with projects.

One user may belong to many projects.

One project may contain many users.

| Field      | Type                       |
| ---------- | -------------------------- |
| id         | bigint                     |
| project_id | foreign key -> projects.id |
| user_id    | foreign key -> users.id    |
| joined_at  | timestamp                  |

---

## Task Statuses

Stores workflow columns.

| Field    | Type    |
| -------- | ------- |
| id       | bigint  |
| name     | string  |
| position | integer |

### Default Values

1. Backlog
2. To Do
3. In Progress
4. Review
5. Done

---

## Tasks

Main work items.

| Field       | Type                            |
| ----------- | ------------------------------- |
| id          | bigint                          |
| project_id  | foreign key -> projects.id      |
| status_id   | foreign key -> task_statuses.id |
| title       | string                          |
| description | text                            |
| priority    | string                          |
| assignee_id | foreign key -> users.id         |
| reporter_id | foreign key -> users.id         |
| deadline    | datetime                        |
| created_at  | timestamp                       |
| updated_at  | timestamp                       |

### Priorities

* low
* medium
* high
* critical

---

## Comments

Stores task discussions.

| Field      | Type                    |
| ---------- | ----------------------- |
| id         | bigint                  |
| task_id    | foreign key -> tasks.id |
| user_id    | foreign key -> users.id |
| content    | text                    |
| created_at | timestamp               |
| updated_at | timestamp               |

---

## Attachments

Stores uploaded files.

| Field         | Type                    |
| ------------- | ----------------------- |
| id            | bigint                  |
| task_id       | foreign key -> tasks.id |
| uploaded_by   | foreign key -> users.id |
| original_name | string                  |
| stored_name   | string                  |
| mime_type     | string                  |
| file_size     | integer                 |
| created_at    | timestamp               |

---

## News

Internal announcements.

| Field      | Type                    |
| ---------- | ----------------------- |
| id         | bigint                  |
| title      | string                  |
| content    | text                    |
| author_id  | foreign key -> users.id |
| created_at | timestamp               |
| updated_at | timestamp               |

---

## Activity Logs

Stores system activity history.

| Field       | Type                    |
| ----------- | ----------------------- |
| id          | bigint                  |
| user_id     | foreign key -> users.id |
| entity_type | string                  |
| entity_id   | bigint                  |
| action      | string                  |
| created_at  | timestamp               |

### Example Actions

* created_project
* updated_project
* created_task
* updated_task
* changed_status
* uploaded_file
* added_comment
* created_news

---

# Relationships

## User Relationships

User

* has many Projects (created_by)
* has many Tasks (assigned_to)
* has many Tasks (reporter_id)
* has many Comments
* has many Attachments
* has many News
* has many ActivityLogs

---

## Project Relationships

Project

* belongs to User (creator)
* has many Tasks
* has many ProjectMembers

---

## Task Relationships

Task

* belongs to Project
* belongs to TaskStatus
* belongs to User (assignee)
* belongs to User (reporter)

Task

* has many Comments
* has many Attachments

---

## Comment Relationships

Comment

* belongs to Task
* belongs to User

---

## Attachment Relationships

Attachment

* belongs to Task
* belongs to User

---

## News Relationships

News

* belongs to User

---

## ActivityLog Relationships

ActivityLog

* belongs to User

---

# Future Extensions (Not Included In MVP)

The following features may be added later:

* Tags
* Subtasks
* Task Watchers
* Notifications
* Reactions
* Mentions
* Time Tracking
* Sprint Management
* Epics
* Team Workspaces
* Calendar View

These features are intentionally excluded from the first version to keep the project focused and achievable.
