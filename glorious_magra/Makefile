# GloriousMagra Planner - Makefile
# ============================================================================

# Variables
DC=docker compose
BACKEND=$(DC) exec backend
FRONTEND=$(DC) exec frontend

.PHONY: help up down build restart logs logs-backend logs-frontend logs-db logs-nginx status ps shell-backend shell-frontend clean install setup backend-install backend-dev

# ============================================================================
# 📖 Help
# ============================================================================
help:
	@echo ""
	@echo "GloriousMagra Planner - Available Commands"
	@echo "=========================================="
	@echo ""
	@echo "🚀 General:"
	@echo "  make up              Start all services in background"
	@echo "  make down            Stop all services"
	@echo "  make build           Build all Docker images"
	@echo "  make restart         Restart all services"
	@echo "  make status          Show running containers"
	@echo "  make logs            Show logs from all services (follow mode)"
	@echo ""
	@echo "📊 Service Logs:"
	@echo "  make logs-backend    Show backend logs"
	@echo "  make logs-frontend   Show frontend logs"
	@echo "  make logs-db         Show database logs"
	@echo "  make logs-nginx      Show nginx logs"
	@echo ""
	@echo "🔧 Container Access:"
	@echo "  make shell-backend   Open shell in backend container"
	@echo "  make shell-frontend  Open shell in frontend container"
	@echo ""
	@echo "📦 Dependencies:"
	@echo "  make install         Install all dependencies (backend + frontend)"
	@echo "  make backend-install Install backend PHP dependencies via Composer"
	@echo "  make setup           Complete setup for fresh project (build, up, install, migrate, fixtures)"
	@echo ""
	@echo "⚙️  Backend (Symfony):"
	@echo "  make console         Run Symfony console command"
	@echo "  make cache-clear     Clear backend cache"
	@echo "  make migrate         Run database migrations"
	@echo "  make fixtures        Load database fixtures"
	@echo ""
	@echo "🧪 Testing & Quality:"
	@echo "  make test            Run backend unit tests"
	@echo "  make test-coverage   Run tests with coverage report"
	@echo "  make lint            Run PHP linter (php-cs-fixer)"
	@echo "  make stan            Run static analysis (PHPStan)"
	@echo "  make validate        Run all checks (tests + linting + analysis)"
	@echo ""
	@echo "⚛️  Frontend (React):"
	@echo "  make frontend-install  Install frontend dependencies"
	@echo "  make frontend-dev      Run frontend dev server"
	@echo "  make frontend-build    Build frontend for production"
	@echo "  make frontend-lint     Lint frontend code"
	@echo ""
	@echo "🐳 Backend Docker:"
	@echo "  make backend-dev       Run backend container in development mode"
	@echo ""
	@echo "🧹 Cleanup:"
	@echo "  make clean           Remove dangling images and unused volumes"
	@echo "  make reset           Reset entire project (warning: deletes data)"
	@echo ""

# ============================================================================
# 🚀 General
# ============================================================================
up:
	$(DC) up -d

down:
	$(DC) down

build:
	$(DC) build

restart:
	$(DC) down && $(DC) up -d

status:
	$(DC) ps

ps: status

logs:
	$(DC) logs -f
# ============================================================================
# 📊 Service Logs
# ============================================================================
logs-backend:
	$(DC) logs -f backend

logs-frontend:
	$(DC) logs -f frontend

logs-db:
	$(DC) logs -f db

logs-nginx:
	$(DC) logs -f nginx

# ============================================================================
# 🔧 Container Access
# ============================================================================
shell-backend:
	$(BACKEND) sh

shell-frontend:
	$(FRONTEND) sh

# ============================================================================
# 📦 Dependencies
# ============================================================================
install:
	make backend-install
	make frontend-install

backend-install:
	$(BACKEND) composer install

backend-dev:
	$(BACKEND) sh

setup: build up install migrate fixtures
	@echo ""
	@echo "✅ Project setup complete!"
	@echo "🌐 Frontend: http://localhost:5173"
	@echo "🔌 Backend API: http://localhost:8080"
	@echo "🗄️  Database: localhost:3306"
	@echo ""

# ============================================================================
# ⚙️ Backend (Symfony)
# ============================================================================
console:
	$(BACKEND) php bin/console

cache-clear:
	$(BACKEND) php bin/console cache:clear

migrate:
	$(BACKEND) php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	$(BACKEND) php bin/console doctrine:fixtures:load --no-interaction

# ============================================================================
# 🧪 Tests & Quality
# ============================================================================
test:
	$(BACKEND) php vendor/bin/phpunit

test-coverage:
	$(BACKEND) php vendor/bin/phpunit --coverage-text

lint: cs-fix
	@echo "✅ Code style fixed"

cs-fix:
	$(BACKEND) php vendor/bin/php-cs-fixer fix

stan:
	$(BACKEND) php vendor/bin/phpstan analyse

validate: test stan
	@echo "✅ All validations passed!"

# ============================================================================
# ⚛️ Frontend (React)
# ============================================================================
frontend-install:
	$(FRONTEND) npm install

frontend-dev:
	$(FRONTEND) npm run dev

frontend-build:
	$(FRONTEND) npm run build

frontend-lint:
	$(FRONTEND) npm run lint

# ============================================================================
# 🧹 Cleanup
# ============================================================================
clean:
	docker image prune -f
	docker volume prune -f
	@echo "✅ Cleaned up dangling images and volumes"

reset:
	$(DC) down -v
	$(DC) up -d --build
	@echo "✅ Project reset complete!"
