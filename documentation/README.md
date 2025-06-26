# Gazeta Internetowa

## Requirements

- **PHP:** 8.1 or higher
- **Composer:** Latest version
- **MySQL:** 8.0 or higher
- **Docker & Docker Compose**

## Installation

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd gazeta-internetowa
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Database Setup
```bash
# Create database
php bin/console doctrine:database:create

# Run migrations
php bin/console doctrine:migrations:migrate

# Load fixtures (sample data)
php bin/console doctrine:fixtures:load --no-interaction
```

### Step 4: Start the Application
```bash
# Using Symfony CLI
symfony server:start

# Or using PHP built-in server
php -S localhost:8000 -t public/
```

## Running Tests

```bash
# Run all tests
php vendor/bin/phpunit

# Run tests with coverage
XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html coverage/

# Run tests with detailed output
php vendor/bin/phpunit --testdox
```

## Generating Documentation

```bash
# Install phpDocumentor (if needed)
curl -L -o phpDocumentor.phar https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.4.3/phpDocumentor.phar
chmod +x phpDocumentor.phar

# Generate API documentation
php phpDocumentor.phar run -d src -t docs/

# Clean up
rm phpDocumentor.phar
```

## 🔍 Code Quality

```bash
# Run code quality check
./check_code_ztp2.sh

# Fix code style issues
./vendor/bin/phpcbf src/

# Check code style
./vendor/bin/phpcs src/
```

## Default Users

After loading fixtures, the following users are available:

### Admin User
- **Email:** `admin@example.com`
- **Password:** `admin123`
- **Role:** `ROLE_ADMIN`

### Regular Users
- Multiple test users are created with emails like `user1@example.com`, `user2@example.com`
- **Password:** `password123`

## Project Structure

```
gazeta-internetowa/
├── src/
│   ├── Command/          # Console commands
│   ├── Controller/       # Web controllers
│   ├── Entity/          # Database entities
│   ├── Form/            # Form types
│   ├── Repository/      # Data access layer
│   ├── Security/        # Authentication & authorization
│   └── Service/         # Business logic
├── templates/           # Twig templates
├── tests/              # Unit and functional tests
├── config/             # Application configuration
├── migrations/         # Database migrations
├── documentation/      # Generated documentation
└── public/            # Web root directory
```

## Features

- **User Management:** Registration, login, profile management
- **Article System:** Create, edit, delete articles with categories
- **Commenting:** Users can comment on articles
- **Admin Panel:** User management, content moderation
- **Multi-language:** English and Polish translations
- **Responsive Design:** Mobile-friendly interface

## Test Coverage

- **Total Tests:** 171
- **Assertions:** 485
- **Coverage:** 69.22% overall
- **Status:** All tests passing ✅

## Development

### Database Migrations
```bash
# Create new migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Reset database
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### Adding New Features
1. Create entity: `php bin/console make:entity`
2. Create migration: `php bin/console make:migration`
3. Create controller: `php bin/console make:controller`
4. Create form: `php bin/console make:form`
5. Write tests in `tests/` directory

## Deployment

### Production Deployment
1. Set `APP_ENV=prod` in environment
2. Clear cache: `php bin/console cache:clear --env=prod`
3. Run migrations: `php bin/console doctrine:migrations:migrate --env=prod`
4. Load fixtures: `php bin/console doctrine:fixtures:load --env=prod`

## Documentation

Complete documentation is available in the `documentation/` folder:
- **API Documentation:** `documentation/api-docs/index.html`
- **Test Results:** `documentation/test-results.txt`
- **Code Quality Report:** `documentation/code-quality-report.txt`

---

**Created by:** Konrad Stomski
**Course:** ZTP2   
**Date:** June 2025 