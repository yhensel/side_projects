# Glorious Magra

Glorious Magra is a full-stack fitness progress application designed around a solo-first experience. Authenticated users can record body measurements, calculate body-fat percentage, review their measurement history, and track goals without joining a group. Optional groups and activity feeds provide a social layer for users who want it.

The application is organized as a React progressive web app backed by a Symfony API. The complete development environment runs with Docker Compose.

## Core Capabilities

- User registration and JWT-based authentication.
- Measurement capture and history for authenticated users.
- Automatic body-fat percentage calculation using anthropometric measurements and the U.S. Navy method.
- Goal and progress tracking.
- Optional groups, invitations, memberships, and activity feeds.
- REST API exposed through Symfony controllers.
- Responsive React frontend with PWA support.

The standalone measurement and progress workflow is the primary product experience. Group participation is optional and must not be required to use the core features.

## Architecture

```text
+----------------------+       HTTPS / JSON API       +----------------------+
| React 19 + Vite PWA  | <--------------------------> | Symfony 7.4 API      |
| frontend/            |                              | backend/             |
+----------------------+                              +----------+-----------+
                                                                    |
                                                                    v
                                                            +---------------+
                                                            | MySQL 8        |
                                                            +---------------+
```

### Backend

The backend is the source of truth for business rules and data validation. It uses:

- PHP 8.2 or newer.
- Symfony 7.4.
- Doctrine ORM and migrations.
- JWT authentication with LexikJWTAuthenticationBundle.
- Refresh tokens with GesdinetJWTRefreshTokenBundle.
- Domain-driven organization under `backend/src/`.
- PHPUnit, PHP CS Fixer, and PHPStan for quality checks.

Important backend domains include users, measurements, body-fat calculations, goals, groups, memberships, and group activity.

### Frontend

The frontend is a React 19 application built with Vite. It uses:

- React Router for navigation.
- Zustand for client-side state.
- A web app manifest and service worker for PWA behavior.
- A shared API integration layer for communicating with the Symfony backend.

## Prerequisites

Install the following tools on the host machine:

- Docker Engine or Docker Desktop with Docker Compose.
- GNU Make.
- Git.

PHP, Composer, Node.js, npm, and MySQL are provided by the Docker services. They do not need to be installed locally for the standard workflow.

## Getting Started

From this directory, build and start the complete environment:

```bash
make setup
```

The setup target builds the images, starts the services, installs backend and frontend dependencies, runs database migrations, and loads fixtures.

For a more incremental setup:

```bash
make build
make up
make install
make migrate
make fixtures
```

The default development endpoints are:

- Frontend: <http://localhost:5173>
- Backend API: <http://localhost:8080>
- MySQL: `localhost:3306`

The backend environment is loaded from `backend/.env`. Keep local secrets and generated credentials out of version control. Use the provided environment distribution files as templates when creating a new environment.

## Common Commands

### Docker and services

```bash
make up                # Start all services in the background
make down              # Stop all services
make restart           # Restart all services
make status            # Show service status
make logs              # Follow logs from all services
make logs-backend     # Follow backend logs
make logs-frontend    # Follow frontend logs
make logs-db          # Follow database logs
make logs-nginx        # Follow Nginx logs
```

### Dependencies and application setup

```bash
make install           # Install backend and frontend dependencies
make backend-install   # Install PHP dependencies with Composer
make frontend-install  # Install frontend dependencies with npm
make migrate           # Apply Doctrine migrations
make fixtures          # Load development fixtures
make cache-clear      # Clear the Symfony cache
```

### Development shells

```bash
make shell-backend     # Open a shell in the backend container
make shell-frontend    # Open a shell in the frontend container
make console           # Run a Symfony console command
```

For example:

```bash
make console COMMAND="about"
```

### Frontend development

```bash
make frontend-dev      # Start the Vite development server
make frontend-build    # Build the frontend
make frontend-lint     # Run the frontend linter
```

### Backend quality checks

Run checks inside Docker through the Makefile:

```bash
make test              # Run PHPUnit tests
make test-coverage     # Run PHPUnit with coverage output
make lint              # Apply PHP CS Fixer
make stan              # Run PHPStan
make validate          # Run tests and static analysis
```

## API Overview

The API is served through Nginx on port `8080`. Authentication uses JWT access tokens, with refresh-token support configured for session renewal.

The main workflows include:

- `POST /api/auth/register` for registration.
- `POST /api/auth/login` for authentication.
- Measurement create, list, and detail operations for authenticated users.
- Group creation, joining by invitation code, membership listing, and activity feeds.

Symfony controllers and application services expose the API and enforce authorization and validation on the backend; frontend validation is only a usability aid.

## Body-Fat Calculation

Measurement creation calculates body-fat percentage on the backend from anthropometric inputs. The U.S. Navy method uses sex-specific equations and measurements such as height, neck, waist, and hip circumference where applicable.

The calculated result is persisted with the measurement so historical results remain available to the user. The calculation service is covered by backend tests and should be changed independently from transport and persistence concerns.

## Repository Layout

```text
backend/                 Symfony API, domain logic, persistence, and tests
docker/                  Dockerfiles and service configuration
doc/                     Product definition, implementation plan, and deployment notes
frontend/                React/Vite PWA
compose.yml              Docker Compose service definitions
Makefile                 Development, setup, and quality commands
```

The backend follows a layered structure:

```text
backend/src/
├── Application/          Use cases and application services
├── Domain/               Domain models, rules, and repository contracts
├── Infrastructure/       Doctrine, HTTP, security, and framework adapters
└── Kernel.php             Symfony application kernel
```

## Documentation

- [Product definition](doc/project_definition.md)
- [Implementation plan](doc/implementation_plan.md)
- [OVH deployment notes](doc/deployment_ovh.md)

## Development Principles

- Keep business rules in the backend domain and application layers.
- Validate every API input on the backend.
- Keep the solo user journey independent from group features.
- Run tests and quality checks through Docker and the Makefile.
- Never commit passwords, private keys, JWT secrets, or other environment credentials.
- Prefer small, focused changes that preserve the existing architecture.
