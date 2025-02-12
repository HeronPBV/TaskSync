# TaskSync - Realtime Kanban Board Management System

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?style=for-the-badge&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js)
![Pinia](https://img.shields.io/badge/Pinia-✓-yellow?style=for-the-badge&logo=pinia)
![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-purple?style=for-the-badge&logo=inertia)
![Node.js](https://img.shields.io/badge/Node.js-18+-green?style=for-the-badge&logo=node.js)
![TypeScript](https://img.shields.io/badge/TypeScript-✓-blue?style=for-the-badge&logo=typescript)
![Pest PHP](https://img.shields.io/badge/Pest-✓-pink?style=for-the-badge&logo=pestphp)
![WebSockets](https://img.shields.io/badge/WebSockets-Socket.io-9cf?style=for-the-badge&logo=socket.io)
![Redis](https://img.shields.io/badge/Redis-✓-red?style=for-the-badge&logo=redis)

![image](https://github.com/user-attachments/assets/d29eb383-e2f6-4f67-b6b4-f9b5d4445643)



## 📌 Introduction

The project is part of the technical assessment for the **Laravel Vue Full Stack Engineer** position at **Curotec**. Its sole purpose is to demonstrate **solid expertise** in **system development** using **Laravel, Vue.js, and related technologies**.

TaskSync is a **modern, full-stack Kanban board management system** built with **Laravel 10, Vue.js 3, and Inertia.js**. Designed for smooth task tracking and project management, it integrates a robust backend with an intuitive, real-time frontend.

This project implements best practices in **architecture, performance optimization, and clean code principles**, ensuring extensibility, maintainability, and efficiency.

---

## ✨ Features Overview
### 🏗 Kanban Board Management
- **Create, Read, Update, and Delete (CRUD) operations** for boards, columns, and tasks.
- **Drag-and-drop reordering** of tasks within columns.
- **Position-based sorting** for tasks and columns.
- **Soft delete implementation**, ensuring data can be recovered when necessary.

### 🔒 Authentication & Authorization
- **User authentication** powered by Laravel Breeze (Jetstream-ready).
- **Authorization policies** enforced on every resource (BoardPolicy, ColumnPolicy, TaskPolicy).

### 🚀 Performance Optimization
- **Eager Loading** implemented throughout the application to prevent N+1 queries.
- **Database Indexing** for high-speed queries.
- **Caching Strategies** using Redis (or File/Database cache as a fallback) to optimize queries.
- **Queued Jobs & Workers** for heavy processing tasks (report generation).

### 📊 Reports & Data Analysis
- **In time Reports** including statistics for created/deleted boards, columns, and tasks.
- **Report Caching** to avoid unnecessary recalculations.
- **Polling Mechanism** in Vue.js to auto-refresh reports when updates occur.

### 🛠 Technical & Developer Features
- **API-Driven Architecture** using Laravel API Resources.
- **Unit & Feature Testing** with Pest 2.
- **WebSockets Implementation** for real-time updates on board.

---

## 🔍 Technical & Architectural Decisions

### **Monolithic Architecture** Instead of Separate Backend & Frontend Projects
- This decision was made with the KISS principle in mind: keep it simple. On his website, Martin Fowler talks about a very interesting concept called Monolith First. Basically, you shouldn't introduce additional distribution complexity into your system unless you have a very good reason to do so, and given the initial scale of this project, there was no such need. Reference: https://martinfowler.com/bliki/MonolithFirst.html

### **Adapter Pattern for Socket.io WebSocket Server**
- Initially, we opted for beyondcode/laravel-websockets, a popular Laravel WebSocket package. However, it was in the process of being deprecated, raising concerns about long-term maintenance and security. To replace it, we considered Soketi, an open-source alternative that mimics Pusher but required an outdated Node.js version, leading to compatibility issues. Another option was using managed services like Pusher or Ably, but these introduced cost concerns and vendor lock-in. Since Socket.io does not have direct Laravel integration, we implemented an Adapter Pattern to bridge Laravel with Socket.io, ensuring: decoupling from a specific WebSocket provider, compatibility with modern Node.js versions and flexibility to switch implementations in the future.

### **Polling Strategy** for Real-Time Updates (Instead of WebSockets for Everything)
- WebSockets are ideal for frequent real-time updates (e.g., live board updates), but using them for everything would consume unnecessary server resources. The waitForNewReport() method in Pinia ensures that even if WebSockets are down, polling still delivers the required updates. I used a recursive polling mechanism with exponential backoff to minimize server load while ensuring timely updates. Also I wanted to demonstrate another option for realtime update. The polling is used only in the report page, when the user click on the "Generate new report" button, the steps can be seen on the browser console.

### **Singleton Pattern** for WebSocket Service
- The WebSocket connection is expensive, and we don’t want multiple instances per request. The Singleton pattern ensures that the same connection instance is reused instead of being re-established repeatedly. By having a single instance, the WebSocket state is shared across all components, making it easier to track event subscriptions and avoid duplicate listeners.

### **Caching Strategy** for Performance Optimization
- We implemented aggressive caching with Laravel’s cache system, ensuring that we avoid unnecessary database queries. Instead of querying the database every time a user accesses a board, we cache the results for a set time (1 hour), and when a board or task is modified, we invalidate only the relevant cache, keeping the system up-to-date without excessive reads. This aproach ensures that as the application scales with more users and boards, we wont have performance degradation.

### **Use of TypeScript** in the front-end
- TypeScript brings many advantages to front-end development: by using static typing, it prevents runtime errors by detecting type-related issues at compile time, improving reliability, and making the codebase easier to understand and refactor. As your project grows, TypeScript helps maintain consistency and reduces technical debt.

## Interesting software engineering techniques used

### Code Structure and Organization
I maintained a clean and logical structure that makes the codebase easy to navigate and extend.

- MVC Architecture:
The backend follows the Model-View-Controller (MVC) pattern, keeping business logic separate from presentation and request handling.
Models handle data interactions, controllers process user requests, and services contain business logic.
- Well-Defined Service Layer:
We extract business logic into service classes, preventing controllers from becoming bloated.
Example: The BoardService, ColumnService, and TaskService encapsulate board, column, and task operations, improving modularity.
- Component-Based Frontend:
The Vue.js frontend is built using modular components, each responsible for a single piece of UI logic.
Example: The BoardView.vue component encapsulates all logic related to displaying boards, ensuring reusability.

### **Separation of Concerns** (SoC)
- **Service Layer:** Business logic is encapsulated in `BoardService`, `ColumnService`, and `TaskService`.
- **Repository Pattern:** Eloquent ORM handles data abstraction, ensuring flexible data retrieval.
- **Policy-Based Authorization:** Implemented in Laravel Policies (`BoardPolicy`, `ColumnPolicy`, `TaskPolicy`).
- **Dedicated Job Queues:** Background jobs (like report generation) handled asynchronously via queues.

### **Efficient Database Design**
- **PostgreSQL 15** is used due to its performance and indexing capabilities.
- **Indexes** added to frequently queried columns (`user_id`, `board_id`, `column_id`, `position`).
- **Soft Deletes** allow recovery of deleted items (`deleted_at` timestamps).

### **Frontend Optimization (Vue 3 + Pinia 2)**
- **Pinia Store** for global state management.
- **Lazy Loading & Code Splitting** for improved page load performance.
- **Debounced API Calls & Polling** for optimized data fetching.

### **Caching Strategies**
- **Redis-backed caching** for storing board data (`user:boards`, `board:details`, etc.).
- **Cache invalidation mechanisms** ensure fresh data after updates.
- **Efficient query caching** using `Cache::remember()`.

### **Security Best Practices**
- **CSRF Protection & Sanctum API Tokens** for secure authentication.
- **Encrypted Passwords (Bcrypt hashing)** ensuring user credentials remain secure.
- **Rate Limiting** on API endpoints to prevent abuse (`throttle:60,1`).

##Adherence to Functional Requirements
Every feature was implemented with the assessment’s criteria in mind, ensuring high completeness and alignment with expectations.

- Authentication & Authorization: ✅
- Task Board with CRUD Operations: ✅
- Real-Time Updates (WebSockets & Polling): ✅
- Performance Optimizations (Indexing & Caching): ✅
- Comprehensive Testing (Pest + PHPUnit): ✅

---

## 🏗 System Components
### 📌 Backend (Laravel 10)
- **Framework:** Laravel 10 (Eloquent ORM, Policies, Queues, Events, WebSockets).
- **Database:** PostgreSQL 15.
- **Caching:** Redis.
- **Authentication:** Laravel Breeze.
- **Testing:** Pest.
- **Queue System:** Redis-backed Laravel Jobs.

### 📌 Socket Server (Socket.io)
- **Lib:** Socket.io.
- **Testing:** Pest.
- Broadcast comunication
- Prevents the user from receiving his own updates

### 📌 Frontend (Vue.js 3 + Inertia.js 1)
- **Framework:** Vue.js 3 (Composition API).
- **State Management:** Pinia.
- **Routing:** Inertia.js (Seamless frontend-backend communication).
- **UI Components:** PrimeVue.
- Centralized Socket Service

---

## ⚙️ Installation & Setup
### 📌 Prerequisites
- PHP 8.1+
- Composer
- Node.js 16+
- PostgreSQL 15
- Redis

### 🛠 Backend Setup
```sh
# Clone the repository
git clone https://github.com/HeronPBV/task-sync.git
cd task-sync

# Install dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# Then run migrations & seeders
php artisan migrate --seed

# Start the application
php artisan serve

# In another terminal, start laravel worker
php artisan queue:work

# In another terminal, start the socket server
cd socket-server
node index.js
```

### 🎨 Frontend Setup
```sh
# Install dependencies
npm install

# Compile assets
npm run dev
```

### 🚀 Running the Application
- Visit `http://localhost:8000`.

---

## ✅ Running Tests
### 🧪 All tests are made with Pest
```sh
php artisan test
```

---

## 📜 API Endpoints Overview
### Boards
| Method | Endpoint              | Description            |
|--------|----------------------|------------------------|
| GET    | `/boards`             | List user boards      |
| POST   | `/boards`             | Create a new board    |
| GET    | `/boards/{id}`        | Get board details     |
| PATCH  | `/boards/{id}`        | Update a board        |
| DELETE | `/boards/{id}`        | Delete a board        |

### Columns
| Method | Endpoint                   | Description            |
|--------|---------------------------|------------------------|
| POST   | `/boards/{board}/columns` | Create a column       |
| PATCH  | `/columns/{id}`           | Update a column       |
| DELETE | `/columns/{id}`           | Delete a column       |

### Tasks
| Method | Endpoint                  | Description            |
|--------|--------------------------|------------------------|
| POST   | `/columns/{column}/tasks` | Create a task         |
| PATCH  | `/tasks/{id}`              | Update a task         |
| DELETE | `/tasks/{id}`              | Delete a task         |

---

## 🔮 Future Improvements
- **Real-time drag-and-drop**, using WebSockets, inside board view.
- **Advanced analytics dashboard** for task trends, with chart.js graphs.
- **Collaboration features** (comments, notifications).

---
