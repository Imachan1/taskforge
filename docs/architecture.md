# Architecture

## High-Level Architecture

TaskForge follows a separated frontend/backend architecture.

Frontend and backend communicate exclusively through REST API.

```text
Vue 3 Frontend
       │
       │ HTTP / JSON
       ▼
Laravel REST API
       │
       ├── MySQL
       │
       └── Elasticsearch
```

---

# Frontend Architecture

Frontend responsibilities:

* UI Rendering
* Routing
* State Management
* API Communication

Technology:

* Vue 3
* Composition API
* Pinia
* Vue Router
* Axios
* PrimeVue
* SCSS

---

# Backend Architecture

Backend responsibilities:

* Authentication
* Authorization
* Business Logic
* Database Access
* Search Indexing

Technology:

* Laravel
* Sanctum
* Eloquent ORM

---

# Infrastructure

Containers:

* frontend
* backend
* mysql
* nginx
* elasticsearch

All services are orchestrated through Docker Compose.

---

# Design Principles

* REST API only
* Frontend and backend are separated
* Business logic should not live inside controllers
* Reusable Vue components
* Single source of truth in Pinia
* Database-first development approach
