#!/bin/bash

# Code inspection script for ZTP2 project
# This script checks:
# 1. Missing translations (locale/pl, locale/en)
# 2. Migrations completeness
# 3. Unit tests execution
# 4. Static code analysis

set -e

echo "=== ZTP2 Project Code Inspection ==="
echo "Starting inspection at $(date)"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local status=$1
    local message=$2
    case $status in
        "OK")
            echo -e "${GREEN}✓ $message${NC}"
            ;;
        "WARNING")
            echo -e "${YELLOW}⚠ $message${NC}"
            ;;
        "ERROR")
            echo -e "${RED}✗ $message${NC}"
            ;;
    esac
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_status "ERROR" "composer.json not found. Please run this script from the project root."
    exit 1
fi

echo "1. Checking translations..."

# Check if translation files exist
if [ -d "translations" ]; then
    if [ -f "translations/messages.en.xlf" ] && [ -f "translations/messages.pl.xlf" ]; then
        print_status "OK" "Translation files exist for both English and Polish"
    else
        print_status "WARNING" "Some translation files are missing"
    fi
    
    # Check for admin translations
    if [ -f "translations/admin.en.xlf" ] && [ -f "translations/admin.pl.xlf" ]; then
        print_status "OK" "Admin translation files exist"
    else
        print_status "WARNING" "Admin translation files are missing"
    fi
else
    print_status "ERROR" "Translations directory not found"
fi

echo ""
echo "2. Checking migrations..."

# Check if migrations directory exists
if [ -d "migrations" ]; then
    migration_count=$(find migrations -name "Version*.php" | wc -l)
    print_status "OK" "Found $migration_count migration files"
    
    # Check if migrations are up to date
    if command -v php >/dev/null 2>&1; then
        if php bin/console doctrine:migrations:status --no-interaction 2>/dev/null | grep -q "up to date"; then
            print_status "OK" "Database is up to date with migrations"
        else
            print_status "WARNING" "Database may not be up to date with migrations"
        fi
    else
        print_status "WARNING" "PHP not available to check migration status"
    fi
else
    print_status "ERROR" "Migrations directory not found"
fi

echo ""
echo "3. Checking unit tests..."

# Check if tests directory exists
if [ -d "tests" ]; then
    test_count=$(find tests -name "*Test.php" | wc -l)
    print_status "OK" "Found $test_count test files"
    
    # Try to run tests if PHP is available
    if command -v php >/dev/null 2>&1; then
        echo "Running unit tests..."
        if php vendor/bin/phpunit --testdox --colors=never 2>/dev/null; then
            print_status "OK" "All unit tests passed"
        else
            print_status "ERROR" "Some unit tests failed"
        fi
    else
        print_status "WARNING" "PHP not available to run tests"
    fi
else
    print_status "ERROR" "Tests directory not found"
fi

echo ""
echo "4. Checking static code analysis..."

# Check PHP CS Fixer
if [ -f "vendor/bin/php-cs-fixer" ]; then
    echo "Running PHP CS Fixer..."
    if php vendor/bin/php-cs-fixer fix --dry-run --diff 2>/dev/null; then
        print_status "OK" "Code style is correct"
    else
        print_status "WARNING" "Code style issues found"
    fi
else
    print_status "WARNING" "PHP CS Fixer not available"
fi

# Check PHP CodeSniffer
if [ -f "vendor/bin/phpcs" ]; then
    echo "Running PHP CodeSniffer..."
    if php vendor/bin/phpcs --standard=PSR12 src/ 2>/dev/null; then
        print_status "OK" "Code standards check passed"
    else
        print_status "WARNING" "Code standards issues found"
    fi
else
    print_status "WARNING" "PHP CodeSniffer not available"
fi

echo ""
echo "5. Checking project structure..."

# Check essential directories
directories=("src" "templates" "config" "public")
for dir in "${directories[@]}"; do
    if [ -d "$dir" ]; then
        print_status "OK" "Directory $dir exists"
    else
        print_status "ERROR" "Directory $dir is missing"
    fi
done

# Check essential files
files=("composer.json" "symfony.lock" "phpunit.xml.dist" ".env")
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        print_status "OK" "File $file exists"
    else
        print_status "WARNING" "File $file is missing"
    fi
done

echo ""
echo "6. Checking data fixtures..."

# Check if fixtures can be loaded
if [ -d "src/DataFixtures" ]; then
    fixture_count=$(find src/DataFixtures -name "*.php" | wc -l)
    print_status "OK" "Found $fixture_count fixture files"
    
    if command -v php >/dev/null 2>&1; then
        echo "Checking if fixtures can be loaded..."
        if php bin/console doctrine:fixtures:load --dry-run --no-interaction 2>/dev/null; then
            print_status "OK" "Fixtures can be loaded successfully"
        else
            print_status "WARNING" "Issues with loading fixtures"
        fi
    fi
else
    print_status "WARNING" "DataFixtures directory not found"
fi

echo ""
echo "=== Inspection Summary ==="
echo "Inspection completed at $(date)"
echo ""
echo "Recommendations:"
echo "1. Ensure all translation files are complete"
echo "2. Run migrations if database is not up to date"
echo "3. Fix any failing unit tests"
echo "4. Fix code style issues if found"
echo "5. Load test data using fixtures"
echo ""
echo "For detailed results, check the output above."