# TaskForge Development Roadmap

## Project Goal

Build a commercial-style project management system using:

* Vue 3
* Pinia
* Vue Router
* Laravel
* REST API
* MySQL
* Docker
* Elasticsearch

The project should demonstrate practical full-stack development skills suitable for Junior PHP/Vue positions.

---

# Phase 0 — Project Setup

## Documentation

* [x] Requirements
* [x] Database Schema
* [x] API Specification

## Environment

* [ ] Install Docker
* [ ] Verify PHP
* [ ] Verify Composer
* [ ] Verify Node.js
* [ ] Verify NPM
* [ ] Verify Git

## Repository

* [ ] Initialize Git repository
* [ ] Create GitHub repository
* [ ] Create initial commit

---

# Phase 1 — Backend Foundation

## Laravel Installation

* [ ] Create Laravel project
* [ ] Configure environment
* [ ] Connect MySQL
* [ ] Run first migration

## Authentication

* [ ] Install Sanctum
* [ ] Configure authentication
* [ ] Login endpoint
* [ ] Logout endpoint
* [ ] Current user endpoint

## User Model

* [ ] Create roles
* [ ] Seed demo users

Milestone:

Users can log in and access protected API endpoints.

---

# Phase 2 — Frontend Foundation

## Vue Installation

* [ ] Create Vue project
* [ ] Configure Vue Router
* [ ] Configure Pinia
* [ ] Configure Axios

## Layout

* [ ] Main layout
* [ ] Sidebar
* [ ] Top navigation
* [ ] User menu

## Authentication UI

* [ ] Login page
* [ ] Logout action
* [ ] Protected routes

## Vue Installation

- [ ] Create Vue project
- [ ] Configure Vue Router
- [ ] Configure Pinia
- [ ] Configure Axios
- [ ] Configure SCSS
- [ ] Install PrimeVue

Milestone:

User can log into the system.

---

# Phase 3 — Projects Module

## Database

* [ ] Create Projects table
* [ ] Create Project Members table

## Backend

* [ ] Project model
* [ ] Project controller
* [ ] Project API

## Frontend

* [ ] Projects page
* [ ] Create project form
* [ ] Edit project form
* [ ] Project details page

Milestone:

Projects can be created and managed.

---

# Phase 4 — Task Statuses

## Database

* [ ] Create Task Statuses table

## Backend

* [ ] Task Status model
* [ ] Status API

## Data

* [ ] Seed default statuses

Milestone:

Workflow statuses exist.

---

# Phase 5 — Tasks Module

## Database

* [ ] Create Tasks table

## Backend

* [ ] Task model
* [ ] Task controller
* [ ] Task API

## Frontend

* [ ] Task list
* [ ] Create task form
* [ ] Edit task form
* [ ] Task details page

Milestone:

Tasks can be created and assigned.

---

# Phase 6 — Kanban Board

## Backend

* [ ] Board data endpoint

## Frontend

* [ ] Kanban layout
* [ ] Status columns
* [ ] Task cards

## Optional

* [ ] Drag and drop

Milestone:

Tasks are displayed in workflow columns.

---

# Phase 7 — Comments Module

## Database

* [ ] Create Comments table

## Backend

* [ ] Comment API

## Frontend

* [ ] Comment list
* [ ] Comment creation
* [ ] Comment editing

Milestone:

Users can discuss tasks.

---

# Phase 8 — File Attachments

## Database

* [ ] Create Attachments table

## Backend

* [ ] File upload
* [ ] File download
* [ ] File deletion

## Frontend

* [ ] Upload component
* [ ] Attachment list

Milestone:

Files can be attached to tasks.

---

# Phase 9 — News Module

## Database

* [ ] Create News table

## Backend

* [ ] News API

## Frontend

* [ ] News feed
* [ ] News details page
* [ ] Create announcement form

Milestone:

Internal announcements are available.

---

# Phase 10 — Activity Log

## Database

* [ ] Create Activity Logs table

## Backend

* [ ] Activity service
* [ ] Activity API

## Frontend

* [ ] Activity feed
* [ ] Project activity page

Milestone:

User actions are tracked.

---

# Phase 11 — Dashboard

## Backend

* [ ] Dashboard statistics endpoint

## Frontend

* [ ] Statistics cards
* [ ] Recent activity
* [ ] Recent news

Milestone:

Dashboard displays project overview.

---

# Phase 12 — Docker

## Infrastructure

* [ ] Create Docker Compose
* [ ] PHP container
* [ ] Nginx container
* [ ] MySQL container
* [ ] Frontend container

Milestone:

Entire application runs using Docker.

---

# Phase 13 — Elasticsearch

## Infrastructure

* [ ] Elasticsearch container

## Backend

* [ ] Index projects
* [ ] Index tasks
* [ ] Index comments
* [ ] Index news

## Frontend

* [ ] Global search page

Milestone:

Global search works across all entities.

---

# Phase 14 — Polish & Portfolio

## Quality

* [ ] Refactor code
* [ ] Remove unused code
* [ ] Review naming consistency

## Documentation

* [ ] Update README
* [ ] Add screenshots
* [ ] Add architecture diagrams

## Portfolio

* [ ] Prepare demo account
* [ ] Prepare presentation

Milestone:

Project is ready for GitHub and interviews.

---

# MVP Definition

The project is considered MVP-complete after:

* Authentication
* Projects
* Tasks
* Statuses
* Comments
* Attachments

Everything after that is an enhancement.

---

# Priority Order

1. Authentication
2. Projects
3. Tasks
4. Statuses
5. Comments
6. Attachments
7. News
8. Activity Log
9. Dashboard
10. Docker
11. Elasticsearch
