# Implementation Plan

This implementation plan is intended to complement `doc/project_definition.md` by defining the backend and frontend development workstreams, phases, and validation checkpoints.

## Objectives

- Build a decoupled API-driven fitness tracking app.
- Implement accurate body fat percentage calculation using anthropometric inputs.
- Support authenticated users in a complete solo experience, including measurements, progress visualization, and goals.
- Make social groups an optional enhancement rather than a prerequisite to using the application.
- Deliver a mobile-friendly React PWA that consumes the Symfony API.

## Backend Implementation Plan

### 1. Technical Foundation [x]

- Use Symfony 7.x and PHP 8.5.
- Configure Docker-based backend environment using the existing `docker/php/Dockerfile` and `compose.yml`.
- Choose PostgreSQL or MySQL and verify database connectivity in Docker.
- Enable Doctrine ORM, migrations, and validation.
- Organize the backend with Domain-Driven Design (DDD) and SOLID principles.

### 2. Core Domain and Security [x]

- Implement `User` entity and repository within a domain layer.
- Add authentication with JWT using `lexik/jwt-authentication-bundle`.
- Secure endpoints with token-based access and role checks.
- Add user profile fields required for body fat calculation: `birth_date`, `biological_sex`, and optional measurement defaults.
- Keep controllers thin: use application services to orchestrate domain behavior and infrastructure services for persistence.

### 3. Measurement Domain and Body Fat Calculation [x]

- Implement `Measurement` entity with required fields:
  - `user_id`
  - `date`
  - `weight`
  - `height`
  - `neck`
  - `waist`
  - `hip`
  - `calculated_fat_percentage`
- Create a dedicated Symfony service for body fat calculation.
- Implement the selected anthropometric formula as part of the service and use it in the measurement creation flow.
- Validate measurements at the API level.

### 4. API and Business Logic [x]

- Create RESTful endpoints for:
  - user registration and login
  - `Measurement` create/list/detail
  - group creation, join by invitation code, and membership listing (optional)
  - feed/events for group activity (optional)
- Ensure measurement, history, and progress endpoints work for authenticated solo users even when no group is involved.
- Implement event generation when a measurement is added.
- Add unit tests for calculation logic and API behavior.
- Measurement endpoint integration tests are complete; the remaining follow-up is adding dedicated tests for the group and activity flows.
- Apply SOLID principles in controllers and services: single responsibility, dependency injection, and explicit interfaces.

### 5. Social Groups and Feed [x]

- Implement `Group` and `Group_User` entities as optional social features.
- Add endpoints for:
  - creating and listing groups
  - joining a group with an invitation code
  - viewing group members
- Add `Post` or `Activity` entity to capture group updates.
- Ensure group feed events are generated from measurements and can be fetched by group members, without affecting the solo user flow.
- Backend social logic is complete; dedicated group/activity tests remain as the only pending testing follow-up.

### 6. Validation and Release Criteria [x]

- Backend acceptance criteria:
  - authenticated user can create and retrieve measurements
  - body fat percentage is calculated automatically
  - group invitation and membership workflows function
  - API schema is stable and documented
  - domain logic is isolated from framework and follows DDD/SOLID structure
- Run Symfony tests using Docker container and verify no PHP compatibility issues with PHP 8.5.

## Frontend Implementation Plan

### 1. Technical Foundation

- Use React 18+ with Vite.
- Configure PWA support including `manifest.json` and Service Worker.
- Build the frontend inside the existing `frontend/` directory.
- Use a state-management solution such as Redux Toolkit or Zustand for auth/session state.

### 2. Concrete Phase 3 Steps: Base Frontend and PWA Capabilities

1. Scaffold the app shell
   - Create the Vite React application in the existing `frontend/` directory if it is not already present.
   - Set up the main layout, app router, and route structure for public pages and authenticated pages.
   - Define a shared API client for all backend calls and centralize the base URL in environment configuration.

2. Implement authentication flow
   - Build login and registration views with controlled form inputs and client-side validation.
   - Integrate the forms with the backend authentication endpoints (`/api/auth/login` and `/api/auth/register`).
   - Store the JWT securely, restore the session on reload, and redirect unauthenticated users to login.
   - Add logout handling and route guards for protected screens.

3. Implement measurement capture and solo-user experience
   - Create a measurement form that collects date, weight, height, neck, waist, and hip values.
   - Validate required fields and numeric ranges on the client before submission.
   - Submit the payload to `POST /api/measurements` and display the returned calculated body fat percentage.
   - Add a history page to list saved measurements and show the latest result and trend information.
   - Add a simple dashboard view that highlights the most recent measurement and basic progress context.

4. Implement core progress and goal screens
   - Add a goal creation/editing screen for the user's fat-loss or muscle-gain objective.
   - Display the current goal alongside the latest measurement and progress status.
   - Keep the experience fully usable without any group participation.

5. Implement optional group and social UI
   - Build screens for creating a group, joining a group with an invitation code, and listing joined groups.
   - Add a group detail page that shows members and recent activity.
   - Display feed/activity items produced by backend group events without blocking the standalone experience.

6. Add PWA basics
   - Configure the web app manifest with installable metadata and a standalone display mode.
   - Register a service worker to cache the app shell and static assets.
   - Add offline fallback behavior for the initial load and core routes.
   - Ensure the app is installable and presents a usable mobile experience.

7. Validation and acceptance checks
   - Verify authentication, measurement creation, history display, and group views end to end against the backend API.
   - Test the app on mobile and desktop screen sizes.
   - Confirm error states, loading states, and empty states behave gracefully.

### 2. Authentication and Session

- Build login and registration pages.
- Integrate with backend JWT authentication.
- Store auth tokens securely and refresh session state on reload.
- Redirect unauthenticated users to login for protected routes.

### 3. Measurement Capture and Results

- Build a measurement form that collects required fields.
- Validate input before submission.
- Submit measurements to the backend and display the returned calculated fat percentage.
- Add a history page to show saved measurements and fat percentage trends.

### 4. Group and Social Experience

- Create interfaces for group actions:
  - create group
  - join group with invitation code
  - list joined groups
- Build a group feed page that shows activity updates and recent measurements from group members.

### 5. Progress Visualization

- Add progress charts using a lightweight chart library (e.g. Chart.js or Recharts).
- Show measurement history over time and compare with goals.
- Build a goal page to capture and display fitness targets.

### 6. PWA and Offline Readiness

- Configure the app for installation with `display: standalone`.
- Cache static assets and static routes.
- Ensure the app can load when offline with cached shell resources.

### 7. Validation and Release Criteria

- Frontend acceptance criteria:
  - authentication flows work end-to-end
  - measurement creation and result display function
  - group membership and feed pages work
  - the app can be installed as a PWA
- Test on desktop and mobile viewport sizes.

## Phased Delivery

### Phase 1: Backend foundations + measurement API [x]

- Symfony project setup
- User and JWT authentication
- Measurement entity and calculation service
- Measurement create/read API endpoints

### Phase 2: Optional group support and activity feed [x]

- Group entities and endpoints
- Group join workflow
- Activity/event feed generation and consumption
- The core solo experience remains fully available throughout this phase
- Backend social logic is implemented; only dedicated group/activity tests remain for full coverage

### Phase 3: Frontend MVP [x]

- [x] React/Vite app scaffolding
- [x] Login/register flows
- [x] Measurement form and history pages
- [x] English/Spanish translation system with device-language default and user-configurable language in profile settings
- [x] Group pages and feed
- [x] Brand theme pass aligned with GloriousMagra logo colors and visual direction

### Phase 4: Progress charts and PWA polish [x]

- [x] Charts for fat percentage and measurement history
- [x] Goal tracking page
- [x] Service Worker and PWA manifest
- [x] Cross-platform UI polish

## Notes

- Keep backend and frontend decoupled through the JSON API.
- Prefer explicit, testable business logic over magic formulas.
- Validate all measurement and biological data both on the client and server.
- Keep documentation in `doc/` synchronized with actual API and UI behavior.
