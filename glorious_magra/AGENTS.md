# AGENTS.md (root)

## 🧭 Project Overview
Fullstack application:

- `frontend/`: React 19 application
- `backend/`: Symfony 7.4 API (DDD architecture)

Backend is the source of truth.

---

## 🧱 Core Principles
- Prefer simple and explicit solutions
- Avoid unnecessary abstractions
- Readability over cleverness
- Small, focused changes
- All documentation and code comments must be in English
- When a task is completed, mark it as done in the implementation plan and keep the project documentation current

---

## 🔐 Security
- Never commit secrets (.env, tokens, API keys)
- Backend validates ALL input
- Frontend is not trusted

---

## 🐳 Docker
- Entire project must run via Docker
- Do not rely on host machine config
- Use `docker compose` for all services

---

## 📦 Git Rules
- Atomic commits
- Clear commit messages
- Respect `.gitignore`

---

## 🧪 Testing & Quality Checks
- **Always run in Docker containers** - Never run tests/linting locally
- Use Make commands for all checks: `make test`, `make lint`, `make stan`, `make validate`
- Backend: unit + integration tests
- Frontend: test critical flows
- Avoid over-testing
- **Available Make commands:**
  - `make test` - Run backend unit tests
  - `make test-coverage` - Run tests with coverage report
  - `make lint` - Run PHP code style fixer
  - `make stan` - Run static analysis (PHPStan)
  - `make validate` - Run all checks together (tests + linting + analysis)
  - `make frontend-lint` - Lint frontend code

---

## 🔨 Command Execution Rules
- **Always use Make commands** when available
- Execute commands **inside Docker containers**, never on the host machine
- When running CLI commands:
  - Use `make console` for Symfony console commands
  - Use `make shell-backend` or `make shell-frontend` to access containers directly
  - Use `docker compose exec <service>` if no Make command exists
- Example: Instead of running `php bin/console` locally, use `make console`

---

## ⚠️ Agent Restrictions
Agents MUST NOT:
- Introduce new frameworks without justification
- Refactor unrelated code
- Duplicate logic between frontend/backend

---

## 🧠 Decision Order
1. Correctness
2. Simplicity
3. Maintainability
4. Performance (only when needed)
