# Backend Development Guidelines (AGENTS.md)

## 🎯 Project Context
**Glorius Magra** is a fitness tracking and social networking application built with Symfony 7.x and API Platform. The backend calculates accurate body fat percentages using anthropometric measurements and manages authenticated users, social groups, and fitness progress tracking.

---

## 🏗️ Architecture: Domain-Driven Design (DDD)

The backend is organized around DDD principles with clear separation of concerns:

```
src/
├── Domain/                    # Core business logic (framework-agnostic)
│   ├── User/                  # User bounded context
│   │   ├── Entity/
│   │   │   └── User.php
│   │   ├── Repository/        # Interface (implemented in Infrastructure)
│   │   │   └── UserRepository.php
│   │   ├── ValueObject/       # Immutable domain objects
│   │   │   └── Email.php
│   │   │   └── BiologicalSex.php
│   │   └── Service/           # Domain services
│   │       └── UserService.php
│   │
│   ├── Measurement/           # Measurement bounded context
│   │   ├── Entity/
│   │   │   └── Measurement.php
│   │   ├── Repository/
│   │   │   └── MeasurementRepository.php
│   │   └── Service/
│   │       └── BodyFatCalculationService.php
│   │
│   └── Group/                 # Group/social bounded context
│       ├── Entity/
│       ├── Repository/
│       └── Service/
│
├── Application/               # Use cases and application services
│   ├── User/
│   │   ├── Command/
│   │   │   ├── RegisterUserCommand.php
│   │   │   └── LoginUserCommand.php
│   │   ├── CommandHandler/
│   │   └── DTO/
│   │       ├── RegisterUserRequest.php
│   │       └── LoginUserResponse.php
│   │
│   ├── Measurement/
│   │   ├── Command/
│   │   ├── CommandHandler/
│   │   └── DTO/
│   │
│   └── Group/
│
├── Infrastructure/            # Framework integration and persistence
│   ├── Persistence/
│   │   └── Doctrine/          # Doctrine ORM mappings and repositories
│   │       ├── Repository/
│   │       │   ├── DoctrineUserRepository.php
│   │       │   └── DoctrineMeasurementRepository.php
│   │       └── Mapping/
│   │
│   ├── Controller/            # API endpoints (thin controllers)
│   │   ├── User/
│   │   │   ├── RegisterController.php
│   │   │   └── LoginController.php
│   │   ├── Measurement/
│   │   └── Health/
│   │
│   ├── Security/
│   │   ├── JwtUserProvider.php
│   │   └── PasswordHasher.php
│   │
│   └── Service/               # Infrastructure services (logging, email, etc)
│
├── Kernel.php                 # Symfony kernel
└── ...
```

---

## ✅ SOLID Principles

### 1. **Single Responsibility Principle (SRP)**
- **User Entity**: Represents user data only. Does NOT handle hashing, validation, or persistence.
- **PasswordHasher Service**: Handles password hashing exclusively.
- **UserRepository**: Manages user persistence exclusively.
- **BodyFatCalculationService**: Calculates body fat percentage only.

**Example:**
```php
// ❌ BAD: Entity doing too much
class User {
    public function register($email, $password) {
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_BCRYPT); // SRP violation!
        return $this;
    }
}

// ✅ GOOD: Separated concerns
class User {
    private string $email;
    private string $passwordHash;
    
    public function __construct(string $email, string $passwordHash) {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }
}

// Hashing handled by dedicated service
class PasswordHasher {
    public function hash(string $plainPassword): string {
        return password_hash($plainPassword, PASSWORD_BCRYPT);
    }
}
```

### 2. **Open/Closed Principle (OCP)**
- **Calculation Formulas**: Can add new body fat formulas (Jackson-Pollock, Katch-McArdle) without modifying existing code.
- **Controllers**: Use dependency injection so logic can be swapped.

**Example:**
```php
interface BodyFatFormulaInterface {
    public function calculate(Measurement $measurement): float;
}

class USNavyFormula implements BodyFatFormulaInterface {
    public function calculate(Measurement $measurement): float { ... }
}

class JacksonPollockFormula implements BodyFatFormulaInterface {
    public function calculate(Measurement $measurement): float { ... }
}

// Usage
class BodyFatCalculationService {
    public function __construct(private BodyFatFormulaInterface $formula) {}
    
    public function calculatePercentage(Measurement $measurement): float {
        return $this->formula->calculate($measurement);
    }
}
```

### 3. **Liskov Substitution Principle (LSP)**
- All repository implementations must satisfy the `UserRepository` interface contract.
- Substituting `DoctrineUserRepository` for a `InMemoryUserRepository` should not break code.

### 4. **Interface Segregation Principle (ISP)**
- Repository interfaces are minimal and focused (e.g., `UserRepositoryInterface`, `MeasurementRepositoryInterface`).
- Services expose only necessary methods (not bloated god-interfaces).

**Example:**
```php
// ✅ GOOD: Segregated interfaces
interface UserRepositoryInterface {
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
}

interface ReadOnlyUserRepositoryInterface {
    public function findByEmail(string $email): ?User;
}

// ❌ BAD: Bloated interface
interface UserServiceInterface {
    public function save(User $user): void;
    public function findByEmail(string $email): ?User;
    public function calculateMetrics(): array;
    public function sendEmail(): bool;
    public function deleteAccount(): void;
    // ... 20 more methods
}
```

### 5. **Dependency Inversion Principle (DIP)**
- **High-level modules** (controllers, services) depend on abstractions (interfaces).
- **Low-level modules** (Doctrine repositories) implement those abstractions.
- Dependencies are **injected** via constructor.

**Example:**
```php
// ✅ GOOD: Depends on abstraction (interface)
class RegisterUserController {
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordHasher $passwordHasher
    ) {}
}

// ❌ BAD: Depends on concrete class
class RegisterUserController {
    public function __construct(
        private DoctrineUserRepository $userRepository
    ) {}
}
```

---

## 📋 Coding Standards

### Naming Conventions
- **Domain Entities**: `User`, `Measurement`, `Group` (business language)
- **Value Objects**: `Email`, `BiologicalSex`, `PasswordHash`
- **Services**: `UserService`, `BodyFatCalculationService`
- **Repositories**: `UserRepository`, `MeasurementRepository` (interface) → `DoctrineUserRepository` (implementation)
- **Controllers**: `RegisterController`, `LoginController`
- **Commands**: `RegisterUserCommand`, `CreateMeasurementCommand`
- **DTOs**: `RegisterUserRequest`, `LoginUserResponse`

### Immutability
- Entities are constructed with all required data
- Use `private readonly` for properties (PHP 8.1+)
- Value Objects are immutable (no setters)

```php
// ✅ GOOD
class User {
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly string $passwordHash,
        private readonly string $firstName,
        private readonly \DateTimeImmutable $birthDate,
        private readonly BiologicalSex $biologicalSex,
        private readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {}
}

// ❌ BAD: Mutable and setters
class User {
    public string $id;
    public string $email;
    
    public function setEmail(string $email): void {
        $this->email = $email;
    }
}
```

### Explicit Interfaces
- All persistence contracts are interfaces
- Dependency injection uses interfaces, not concrete classes

### Exception Handling
- Create domain exceptions for business logic failures
- Use application exceptions for invalid use-case invocations

```php
namespace Domain\User\Exception;

class UserAlreadyExistsException extends \Exception {}
class InvalidEmailFormatException extends \Exception {}

namespace Application\User\Exception;

class RegistrationFailedException extends \Exception {}
```

---

## 🧪 Testing Strategy

### Unit Tests
- Test domain entities, value objects, and services in isolation
- Mock repositories and infrastructure
- Located in `tests/Unit/`

### Integration Tests
- Test controller → service → repository chains with real or in-memory database
- Located in `tests/Integration/`

### Test Commands
```bash
make test                # Run all tests
make test-coverage       # Run tests with coverage report
```

---

## 🔐 Security

### Password Handling
- Passwords are NEVER stored in code or logged
- Use `PasswordHasher` service for all hashing
- Use bcrypt or argon2 (PHP's `password_hash()` function)

### JWT Authentication
- Use `lexik/jwt-authentication-bundle` for stateless authentication
- Store tokens in secure HTTP-only cookies or Authorization headers
- Validate token signatures on every request

### Input Validation
- Backend validates ALL input (frontend validation is optional UX)
- Use Symfony validators on DTOs and entities

---

## 🔄 Workflow

### Adding a New Feature (Example: Create Measurement Endpoint)

1. **Define Domain Entity** (`src/Domain/Measurement/Entity/Measurement.php`)
   - What data does a measurement contain?
   - What rules apply? (business logic)

2. **Create Repository Interface** (`src/Domain/Measurement/Repository/MeasurementRepository.php`)
   - How will we persist measurements?

3. **Create Application Service/Command** (`src/Application/Measurement/Command/CreateMeasurementCommand.php`)
   - What does the user want to do?
   - Define request DTO with validation

4. **Implement Command Handler** (`src/Application/Measurement/CommandHandler/CreateMeasurementCommandHandler.php`)
   - Orchestrate domain services
   - Call repository to persist

5. **Create Infrastructure Layer**
   - `src/Infrastructure/Persistence/Doctrine/Repository/DoctrineMeasurementRepository.php`
   - `src/Infrastructure/Controller/Measurement/CreateMeasurementController.php`

6. **Write Tests**
   - Unit test: `tests/Unit/Domain/Measurement/Service/BodyFatCalculationServiceTest.php`
   - Integration test: `tests/Integration/Controller/CreateMeasurementControllerTest.php`

---

## 📚 Tools and Commands

### Make Commands (Docker-based)
```bash
make shell-backend          # Open backend container shell
make console               # Run Symfony console commands
make test                  # Run PHPUnit tests
make test-coverage         # Run tests with coverage
make lint                  # Run PHP code style fixer
make stan                  # Run PHPStan static analysis
make validate              # Run all checks (tests + lint + stan)
make migrate               # Run database migrations
make migrate-diff          # Generate migration from entity changes
```

### Useful Symfony Commands (inside container)
```bash
php bin/console make:entity User
php bin/console make:controller
php bin/console doctrine:database:create
php bin/console doctrine:migrations:generate
php bin/console doctrine:migrations:migrate
```

---

## 📖 References

- **Symfony Documentation**: https://symfony.com/doc/current/
- **API Platform**: https://api-platform.com/docs/
- **Domain-Driven Design**: Eric Evans, "Domain-Driven Design" book
- **SOLID Principles**: Robert C. Martin articles
- **Doctrine ORM**: https://www.doctrine-project.org/
