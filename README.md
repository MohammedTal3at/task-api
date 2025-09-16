
# Task Management API

A RESTful API for managing tasks, built with **Laravel 12**.  
This project features a clean, service-oriented architecture, token-based authentication with Laravel Sanctum, and role-based access control.

The application is fully containerized using **Laravel Sail** and Docker, allowing for a consistent and simple setup process on any machine.

---

## Features

- **Full CRUD Operations:** Create, read, update, and delete functionality for Tasks and Tags.
- **Token-Based Authentication:** Secure user authentication using Laravel Sanctum API tokens.
- **Role-Based Authorization:** Certain endpoints are protected and accessible only by users with an admin role.
- **Advanced Task Filtering:** Rich query parameters supported for task listing:
    - Filter by multiple statuses, priorities, assignees, and tags.
    - Filter by a due date range.
    - Full-text search on task titles and descriptions.
- **Sorting & Pagination:** Supports both page-based and cursor-based pagination.
- **Optimistic Concurrency Control:** Prevents data conflicts when multiple users update the same task simultaneously.
- **Service-Oriented Architecture:** Business logic separated into service classes and DTOs for maintainability and testability.

---

## Tech Stack

- **Backend:** Laravel 12, PHP 8.4
- **Database:** MySQL 8.0
- **Web Server:** Nginx
- **Containerization:** Docker & Laravel Sail
- **Database Management:** phpMyAdmin (http://localhost:8888 by default)

---

## 🚀 Getting Started

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) must be installed and running.

### Installation

The project includes an automated setup script.

```bash
git clone <repository>
cd <repository>
chmod +x setup.sh
./setup.sh
```

This script will:
- Copy `.env.example` to `.env`
- Build and start the required Docker containers (Laravel, MySQL, phpMyAdmin)
- Install all Composer dependencies
- Generate a new application key
- Run database migrations
- Seed the database with initial data (users, tags, etc.)

Once finished, the API will be accessible at:  
**http://localhost** (or your configured `APP_PORT`).

---

## API Endpoints

### Authentication

| Method | Endpoint     | Description                                      |
|--------|-------------|--------------------------------------------------|
| POST   | /api/login  | Authenticates a user and returns an API token     |
| POST   | /api/logout | Revokes the user’s current token (Requires Auth)  |

**Login Request (Admin):**
```json
{ "email": "admin@example.com", "password": "password" }
```

**Login Request (Regular User):**
```json
{ "email": "user_1@example.com", "password": "password" }
```
**Login Response (Regular User):**
```json
{
    "data": {
        "token": "7|34mRlFh2U3uHIkCOA7rAFNDcIKH5daUOfbwj6XPBde40cc66",
        "name": "User_1",
        "role": "user"
    }
}
```

Include token in the `Authorization` header for protected routes:
```
Authorization: Bearer <token>
```

---

### Tasks

| Method | Endpoint                  | Description                    | Authorization |
|--------|---------------------------|--------------------------------|---------------|
| GET    | /api/tasks                | Get a paginated list of tasks  | User          |
| POST   | /api/tasks                | Create a new task              | Admin         |
| GET    | /api/tasks/{task}         | Get a single task by ID        | User          |
| PUT    | /api/tasks/{task}         | Update a task                  | Admin         |
| DELETE | /api/tasks/{task}         | Delete a task                  | Admin         |
| PATCH  | /api/tasks/{task}/toggle-status | Toggle a task’s status | User          |
| PATCH  | /api/tasks/{task}/restore | Restore a soft-deleted task    | Admin         |

**Create Task (POST /api/tasks):**
```json
{
  "title": "Design new API documentation structure",
  "description": "Create a new, more detailed README and Swagger/OpenAPI spec.",
  "priority": "high",
  "due_date": "2025-10-15",
  "assigned_to": 2,
  "tags": [1, 3]
}
```

**Update Task (PUT /api/tasks/{task}):**
> Note: Include the `version` field to prevent concurrency issues.
```json
{
  "status": "in_progress",
  "assigned_to": 3,
  "tags": [1, 2],
  "version": 1
}
```

---

### Tags

| Method | Endpoint         | Description                 | Authorization |
|--------|-----------------|-----------------------------|---------------|
| GET    | /api/tags       | List all tags               | Admin         |
| POST   | /api/tags       | Create a new tag            | Admin         |
| GET    | /api/tags/{tag} | Get a single tag by ID      | Admin         |
| PUT    | /api/tags/{tag} | Update a tag                | Admin         |
| DELETE | /api/tags/{tag} | Delete a tag                | Admin         |

**Create Tag (POST /api/tags):**
```json
{ "name": "Frontend", "color": "#3490DC" }
```

**Update Tag (PUT /api/tags/{tag}):**
```json
{ "name": "Frontend Team", "color": "#227DC7" }
```

---

## Advanced Task Listing

The `GET /api/tasks` endpoint supports extensive filtering, sorting, and pagination.

### Filtering
- Example:
  ```
  /api/tasks?status=open,in_progress&tags=1,2
  ```
- **Date Range:**
    - `due_date_start` (Y-m-d)
    - `due_date_end` (Y-m-d)
- **Keyword Search:**
    - `keyword` (string, min 3 characters) → searches titles and descriptions.

### Sorting
- `sort_by`: created_at, due_date, priority, title (default: created_at)
- `sort_order`: asc, desc

Example:
```
/api/tasks?sort_by=due_date&sort_order=asc
```

### Pagination

**Page-based (default):**
- `page` (default: 1)
- `per_page` (default: 15, max: 100)

**Cursor-based:**
1. Request with `cursor` parameter. → response includes `next_cursor`.
2. Use `next_cursor` in the next request.
3. Continue until `next_cursor` is `null`.

_Example Flow:_
1. `GET /api/tasks?per_page=10&cursor=true`
2. Response → `meta: { "next_cursor": "eyJpZCI6MTB9" }`
3. `GET /api/tasks?per_page=10&cursor=eyJpZCI6MTB9`

---

## 🧪 Running Tests

Run the test suite:
```bash
./vendor/bin/sail test
```
