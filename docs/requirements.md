# TaskForge

## Project Overview

TaskForge is a web-based project and task management platform designed for small teams, software development groups, freelancers, and agencies.

The system allows users to create projects, manage tasks, collaborate through comments, upload files, track progress through workflow stages, and quickly find information using global search.

The primary goal of TaskForge is to provide a lightweight alternative to enterprise project management systems while maintaining a modern architecture based on Vue.js, Laravel, REST API, Docker, and Elasticsearch.

---

# Business Goals

The system should help teams:

* Organize projects
* Manage work items
* Track task progress
* Collaborate through comments
* Store project-related files
* Monitor project activity
* Search across all project data

---

# User Roles

## Administrator

Can:

* Manage users
* Manage projects
* Manage project members
* Manage statuses
* View all data
* Delete any entity

## Project Manager

Can:

* Create projects
* Create tasks
* Assign tasks
* Manage project workflow
* Upload files
* Create announcements

## Team Member

Can:

* View assigned projects
* Update task statuses
* Add comments
* Upload attachments
* Participate in discussions

---

# Functional Modules

## Authentication Module

Features:

* Login
* Logout
* Password hashing
* Session handling
* Role-based access control

Pages:

* Login
* User Profile

---

## Dashboard Module

Purpose:

Provide a high-level overview of work.

Widgets:

* Active projects count
* Tasks assigned to me
* Overdue tasks
* Recent activity
* Recent announcements

---

## Projects Module

Purpose:

Store and organize work.

Project fields:

* Name
* Description
* Status
* Created By
* Created Date

Actions:

* Create project
* Edit project
* Archive project
* Add members
* Remove members

Project View Tabs:

* Overview
* Board
* Files
* Activity

---

## Tasks Module

Purpose:

Track work items.

Task fields:

* Title
* Description
* Priority
* Status
* Assignee
* Reporter
* Deadline
* Created Date

Priorities:

* Low
* Medium
* High
* Critical

Actions:

* Create task
* Edit task
* Delete task
* Assign task
* Change status
* Add comments
* Upload files

---

## Workflow Module

Purpose:

Track task progress.

Default statuses:

* Backlog
* To Do
* In Progress
* Review
* Done

Tasks move through workflow stages.

The board view displays tasks grouped by status.

---

## Comments Module

Purpose:

Enable collaboration.

Features:

* Add comments
* Edit own comments
* Delete own comments
* Show author information
* Show timestamps

---

## File Management Module

Purpose:

Store project and task attachments.

Supported examples:

* Images
* PDF documents
* ZIP archives
* Text files

Features:

* Upload file
* Download file
* Delete file
* Preview images

Files can be attached to:

* Projects
* Tasks

---

## News Module

Purpose:

Provide internal communication.

Announcement fields:

* Title
* Content
* Author
* Publish Date

Features:

* Create announcement
* Edit announcement
* Delete announcement
* View announcement feed

---

## Activity Log Module

Purpose:

Track changes in the system.

Examples:

* Task created
* Status changed
* File uploaded
* Comment added

Activity fields:

* User
* Action
* Entity
* Timestamp

---

## Global Search Module

Purpose:

Provide fast access to information.

Search targets:

* Projects
* Tasks
* Comments
* Files
* News

Example:

Search query:

"login"

Results:

Task:
Implement Login API

Comment:
Login endpoint returns 401

Project:
Authentication Refactoring

News:
Authentication migration completed

---

# User Interface

## General Layout

Top Navigation Bar

Contains:

* Logo
* Search
* Notifications
* User Menu

Left Sidebar

Contains:

* Dashboard
* Projects
* Tasks
* News
* Search Results

Main Content Area

Displays current page content.

---

## Project Board View

Kanban-style layout.

Columns:

Backlog

To Do

In Progress

Review

Done

Each task card displays:

* Title
* Priority
* Assignee
* Due Date

---

## Task Details View

Sections:

* Description
* Metadata
* Attachments
* Comments
* Activity History

The task page should resemble a professional issue tracking system.

---

# Non-Functional Requirements

Performance:

* API responses below 500 ms for common requests

Security:

* Authentication required
* Password hashing
* Authorization checks

Scalability:

* REST API architecture
* Elasticsearch integration

Maintainability:

* Modular structure
* Service-based business logic
* Separation of frontend and backend

Deployment:

* Docker Compose environment
* Separate containers for:

  * Frontend
  * Backend
  * MySQL
  * Elasticsearch
  * Nginx

---

# Technology Stack

## Frontend

- Vue 3
- Composition API
- Vue Router
- Pinia
- Axios
- SCSS
- PrimeVue

## Backend

- Laravel
- REST API
- Laravel Sanctum

## Database

- MySQL

## Infrastructure

- Docker
- Docker Compose

## Search

- Elasticsearch

---

# UI Framework

TaskForge uses PrimeVue components as a foundation for:

- Data Tables
- Dialogs
- Forms
- Toast Notifications
- Menus
- Inputs

Custom styling is implemented using SCSS.