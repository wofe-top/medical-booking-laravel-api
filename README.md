# Medical Booking Laravel API

A modern, secure, and scalable RESTful API built with Laravel for managing medical appointments, doctor profiles, schedules, and patient workflows. This project serves as the backend for a medical booking platform and reflects strong backend engineering practices, clean architecture, and production-ready design.

## 🚀 Key Architectural Features & Patterns

To demonstrate professional backend proficiency and maintainability, this API completely avoids monolithic controller structures and strictly follows elite architectural design patterns:

*   **Separation of Concerns (Service Layer):** All core business logic, medical workflows, and authentication operations are encapsulated inside dedicated **Domain Services** (e.g., `AuthenticationService`, `DoctorService`, `AppointmentService`), keeping controllers thin, clean, and single-purpose.
*   **Contract-Driven Notification Abstraction (`Interface` & IoC Binding):** Message delivery workflows (e.g., patient appointment confirmations) adhere strictly to an explicit contract interface (`NotificationServiceInterface`). By leveraging Laravel’s Service Container binding (`bind()`), concrete notification drivers (e.g., `SmsNotificationService`, `WhatsAppNotificationService`) can be swapped system-wide via a single configuration line without altering core domain business logic.
*   **Unified Multi-Role Authentication:** Supports a unified authentication entry point (`/api/auth/register`) differentiating between `patients` and `doctors`. Includes conditional validation workflows that dynamically require professional profiles and operational shift patterns only if the registrant is a medical practitioner.
*   **Relational Database Normalization (Schedules):** Doctor availability is managed via a dedicated `doctor_schedules` schema utilizing unsigned integers (`day_of_week`: 0-6) mapped to strict `time` datatypes, providing indexable performance and day-specific shift flexibility.
*   **Algorithmic Overlap & Collision Avoidance:** Implements a mathematically sound time-overlap algorithm (`existing_start < new_end` AND `existing_end > new_start`) built strictly within Eloquent constraints to entirely eliminate the possibility of double-booking an individual medical practitioner.
*   **Atomic Database Transactions (`DB::transaction`):** Crucial multi-table write operations—such as creating a user account alongside their medical profile and rotational schedule—are wrapped completely inside transaction blocks to prevent partial data corruption or orphaned database records.
*   **The Query Filters Pattern (Advanced Data Filtering):** High-performance, multi-parameter data filtration is strictly managed on the backend using a dynamic, reusable query engine (`QueryFilter` abstract class and `Filterable` Trait). This enables clean, single-line controller filtering for complex relations, such as filtering doctors by specialties using optimized Eloquent `whereHas` queries.
*   **Advanced Testing Strategy (Feature & Isolated Unit Tests):** Built with stability in mind. Features a comprehensive automated test suite leveraging **Feature Tests** (with isolated `RefreshDatabase` states) to validate complete HTTP request/response lifecycles, and **Pure Unit Tests** (using native PHPUnit `TestCase`) to benchmark core business algorithms completely isolated from the database for instantaneous execution (`0.00s`).
*   **Data Validation (Form Requests):** Incoming HTTP requests are intercepted and validated using specialized **Form Requests** (e.g., `LoginRequest`, `RegisterRequest`) to guarantee strict data integrity before hitting the core application logic.
*   **Data Transformation (API Resources):** JSON responses are strictly formatted through **API Resources**, ensuring a decoupled API contract, safeguarding database columns, and securely handling relational data loading.
*   **Global Exception Handling:** Custom application errors are caught gracefully using a unified, custom **BusinessException** class combined with Laravel's global exception handling. This guarantees consistent, standardized JSON error payloads across all failure states and prevents raw server crashes (500 errors).

## 🛠️ Tech Stack

*   **Framework:** Laravel 11 (PHP 8.x)
*   **Authentication:** Laravel Sanctum (Secure Token-based architecture)
*   **Database:** MySQL (Strict relational integrity)
*   **Testing Suite:** PHPUnit / Laravel Test Runner
*   **API Documentation:** Laravel Scribe (OpenAPI / Postman Integration)

---

## 📊 Project Status & Roadmap

### 🏁 Phase 1: Core Infrastructure & Identity Architecture (Completed)
*   [x] Relational Database Schema design with structured migrations (Users, Doctor Profiles, Specialties, Appointments).
*   [x] Implementation of the dedicated `Service Layer` to isolate business logic.
*   [x] Custom Validation Engine utilizing conditional rules via dedicated `Form Requests`.
*   [x] Standardized API output payload formatting and pagination handling using `API Resources`.
*   [x] Robust Global Exception Handling and Custom `BusinessException` integration.
*   [x] Advanced Backend-Driven Query Filtering Engine for flexible data searching (Doctors and Appointments).
*   [x] Secure User Registration and Login architecture via `AuthenticationService` supporting Unified Multi-Role Flows.
*   [x] Automated transactional profile and schedule mapping for doctor signups.

### ⏳ Phase 2: Booking Engine & Medical Workflows (Completed & Secured)
*   [x] Implementing core Appointment Booking creation endpoints protected via `DB::transaction` blocks.
*   [x] High-performance Algorithmic Collision Prevention engine to fully regulate real-time scheduling.
*   [x] Strict operational validation ensuring appointments are only placed within validated doctor availability windows.
*   [x] Contract-based Notification Architecture (`NotificationServiceInterface`) supporting interchangeable drivers (SMS / WhatsApp) injected through Laravel's Service Container.
*   [x] Customizing Global Exception Handler overrides for `ModelNotFoundException` and `ValidationException` format synchronization.
*   [x] Developing the Dynamic Available Time Slots Calculation API Engine (Refactored into pure functions for decoupled testability).

### 🔮 Phase 3: Testing & Quality Assurance (Completed)
*   [x] Automated Feature Testing for User Registration & Authentication pipelines (Patient and Doctor multi-table workflows).
*   [x] Automated Feature Testing for the Login lifecycle ensuring strict validation and secure Sanctum token issuance.
*   [x] Pure, high-performance Unit Testing for the `Available Slots` time-cutting algorithm (Validating accurate time partitioning and overlapping booked slot exclusion).
*   [x] Complete API Documentation (Interactive OpenApi Web Docs & Postman Collection generated via Scribe).

---

## ⚙️ Installation & Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/wofe-top/medical-booking-laravel-api.git
   cd medical-booking-laravel-api

2. Install dependencies:
    ```bash
    composer install

    ```


3. Copy environment file and configure database:
    ```bash
    cp .env.example .env
    php artisan key:generate

    ```


4. Run migrations & seeders:
    ```bash
    php artisan migrate --seed

    ```


5. Run the automated test suite:
    ```bash
    php artisan test

    ```


6. Generate or view API Documentation:
    ```bash
    php artisan scribe:generate

    ```
