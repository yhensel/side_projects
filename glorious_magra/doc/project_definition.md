## Methodological Correction: The BMI Error in Athletes

The initial premise presents a technical and biological contradiction: **Body Mass Index (BMI) does not measure body fat percentage**, and it is a flawed indicator for athletes.

BMI only correlates weight and height ($BMI = \frac{\text{weight}}{\text{height}^2}$). A highly muscular athlete will yield an elevated BMI (falsely classifying them as overweight or obese), despite having a low body fat percentage.

To fulfill the objective of "calculating body fat percentage for an athlete using body measurements," the application must not use standard BMI. It must implement anthropometric equations based on body circumferences (such as the **U.S. Navy Method**) or skinfold thickness (Jackson-Pollock).

---

## System Architecture (GloriusMagra)

The system will be split into two decoupled layers connected via a RESTful API.

### Core Product Requirement: Solo-First Experience

The application must be fully usable by an authenticated user without joining or creating any group. Measurement tracking, body fat calculation, progress history, and goal tracking are the core user journey. Group features are optional social enhancements and must not block the standalone experience.

```
[ Frontend: React PWA ] <--- HTTPS / JSON API ---> [ Backend: Symfony API Platform ]
                                                           |
                                                   [ Database: PostgreSQL / MySQL ]

```

### Backend: PHP / Symfony

* **Core:** Symfony 7.x (API skeleton or API Platform to accelerate development).
* **Authentication:** JWT (JSON Web Tokens) via `lexik/jwt-authentication-bundle`.
* **Persistence:** Doctrine ORM with PostgreSQL or MySQL.

### Frontend: ReactJS PWA

* **Core:** React 18+ (Vite as the bundler to efficiently configure the Service Worker).
* **Global State:** Redux Toolkit or Zustand (for session management and offline data capabilities).
* **PWA Capabilities:** `manifest.json` configured for native behavior (`display: standalone`) and Service Workers for caching static assets.

---

## Database Design (Core Entities)

To support social and measurement features, the following minimum relational structure is required:

* **User:** `id`, `email`, `password_hash`, `first_name`, `birth_date`, `biological_sex` (required for fat percentage formulas), `created_at`.
* **Group:** `id`, `name`, `invitation_code` (for privacy), `creator_id`.
* **Group_User:** Pivot table for the N:M relationship of users within groups.
* **Measurement:** `id`, `user_id`, `date`, `weight`, `height`, `neck`, `waist`, `hip` (measurements required for the Navy method), `calculated_fat_percentage`.
* **Goal:** `id`, `user_id`, `goal_type` (lose fat / gain muscle), `target_value`, `deadline`.
* **Post/Activity:** `id`, `user_id`, `group_id`, `content` (e.g., "User X logged new progress!"), `created_at`.

---

## Phase-by-Phase Development Plan

### Phase 1: Foundations and Metrics API (Backend)

1. Configure the Symfony project and the database.
2. Implement the `User` entity and JWT authentication.
3. Develop the `Measurement` endpoint. **Mandatory Logic:** Program a Symfony service that automatically calculates the body fat percentage based on the user's biological sex before persisting the data.

> **U.S. Navy Formula for Men (Algorithmic implementation example):**
> 
> $$\% \text{Fat} = 495 / (1.0324 - 0.19077 \cdot \log_{10}(\text{waist} - \text{neck}) + 0.15456 \cdot \log_{10}(\text{height})) - 450$$
> 
> 

### Phase 2: Social Logic and Groups (Backend, Optional Enhancement)

1. Create endpoints for `Groups` management (creation, joining via code).
2. Implement the internal group "Feed" system, where automated events are triggered every time a member adds a weekly measurement.
3. Ensure that all core measurement and progress features remain fully available even when the user is not part of any group.

### Phase 3: Base Frontend and PWA Capabilities (Frontend)

1. Scaffold the React/Vite frontend in the existing `frontend/` directory and establish the app shell, routing, and shared API client configuration.
2. Implement authentication views for login and registration, integrate them with the backend JWT endpoints, and secure protected routes with session persistence.
3. Build a standalone measurement experience for authenticated users: a measurement capture form, client-side validation, submission to the backend, and a history/dashboard view that displays saved measurements and calculated body fat results.
4. Add a basic goal/progress screen so users can track an objective without needing to join a group.
5. Implement optional group-related screens for creating groups, joining via invitation code, and viewing group activity, while keeping the solo experience fully functional without them.
6. Configure the app as a Progressive Web App with a manifest, service worker caching, and installable/mobile-friendly behavior.

### Phase 4: Integration and Progress Visualization

1. Connect React with the Symfony API.
2. Implement time-series progress charts (using libraries like Chart.js or Recharts) to contrast weekly measurements against the established `Goal`.
3. Develop the private social network interface (the group wall showing updates from friends).

---

## Development Progress

### Phase 1: Foundations and Metrics API (Backend)

**Overall Status: ~50% Complete**

#### 1-1: Symfony Project Setup and Configuration
- ✅ Symfony 7.4 initialized
- ✅ Doctrine ORM configured
- ✅ API Platform 4.3 setup
- ✅ Docker environment (PHP, MySQL, Nginx)
- ✅ Database connectivity verified
- ✅ Health check endpoint working

#### 1-2: User Entity and JWT Authentication
- ✅ User entity created with DDD architecture
- ✅ Email value object with validation
- ✅ BiologicalSex enum for sex-specific calculations
- ✅ JWT authentication bundle installed
- ✅ PasswordHasher service implemented
- ✅ JwtUserProvider configured for security
- ✅ Login endpoint working (/api/auth/login)
- ✅ Register endpoint working (/api/auth/register)
- ✅ JWT token generation and validation
- ✅ Database migration applied (users table created)
- ✅ Doctrine custom types configured (EmailType)

#### 1-3: Measurement Entity and Body Fat Calculation
- ✅ Measurement entity created with DDD architecture
- ✅ Measurement repository interface and Doctrine implementation created
- ✅ BodyFatCalculationService implemented with the U.S. Navy tape-measure method
- ✅ Measurement creation endpoint working (`POST /api/measurements`)
- ✅ Measurement list endpoint working (`GET /api/measurements`)
- ✅ Measurement detail endpoint working (`GET /api/measurements/{id}`)
- ✅ Measurement input validation added at the API level
- ✅ Body fat percentage is calculated before persistence

#### 1-4: API Testing and Validation
- ✅ Unit tests added for body fat calculation
- ✅ Integration tests for measurement endpoints completed

### Phase 2: Social Logic and Groups (Backend)
- ✅ Backend social logic and group/activity support implemented
- ✅ Dedicated group/activity tests completed

### Phase 3: Base Frontend and PWA Capabilities (Frontend)
- ⏳ Not started

### Phase 4: Integration and Progress Visualization
- ⏳ Not started

---

**Recommended Next Steps:**
1. Expand API documentation for measurement and group request/response payloads
2. Continue with frontend Phase 3 work for authentication and measurement flows
3. Prepare progress visualization and PWA polish in Phase 4

**Completed in This Session:**
- Backend AGENTS.md with DDD architecture and SOLID principles
- User domain entity with value objects
- Database migration for users table
- JWT authentication setup
- Login and register endpoints
- Password hashing service

Which anthropometric measurement method do you prefer to implement natively in the backend for the body fat calculation (e.g., the U.S. Navy tape-measure method or the Jackson-Pollock caliper method)?
