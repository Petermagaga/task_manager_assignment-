# Task Manager API (Laravel)

## 📖 Overview
This is a Task Management API built using Laravel.  
It allows users to create, manage, and track tasks while enforcing strict business rules.

---

## 🚀 Features
- Create tasks with validation rules
- List tasks with sorting (priority & due date)
- Update task status with strict transitions
- Delete tasks (only when completed)
- Daily task report 

---

## 🛠️ Tech Stack
- Laravel (PHP Framework)
- MySQL (Local Development)
- PostgreSQL (Railway Deployment)
- RESTful API

---

## ____ How toRun Locally____

1. Clone the repository

```bash
git clone https://github.com/Petermagaga/task_manager_assignment-.git
cd task-manager-api

2. Install dependencies
composer install

3. Setup environment

cp .env.example .env
php artisan key:generate

4. Configure MySQL in .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=

5. Run migrations

php artisan migrate

6. Start the server

php artisan serve

API will run at:

http://127.0.0.1:8000
🌐 Deployment (Railway)
Steps used:
Push project to GitHub
Create a new project on Railway
Add MySQL database
Configure environment variables:
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=TydRHjHqLTvJfBANyIQqcdtRLKPWdOkX

Run migrations:
php artisan migrate --force

__ Live URL__

https://taskmanagerassignment-production.up.railway.app

 Note: The app is hosted on Railway free tier and may take a few seconds to respond if inactive.

 API Endpoints & Example Requests

1. Create Task

POST /api/tasks

{
  "title": "Presentation of Final Customer care project",
  "due_date": "2026-03-31",
  "priority": "high"
}
2. List Tasks

GET /api/tasks

Optional:

/api/tasks?status=pending

3. Update Task Status

PATCH /api/tasks/{id}/status

{
  "status": "in_progress"
}
4. Delete Task

DELETE /api/tasks/{id}

5. Daily Report 

GET /api/tasks/report?date=YYYY-MM-DD

_____Business Rules Implemented_____
Unique task title per due date
Due date must be today or future
Priority must be low, medium, or high
Status transitions:
pending → in_progress → done
Only completed tasks can be deleted