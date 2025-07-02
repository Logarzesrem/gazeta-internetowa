#!/bin/bash

echo "🚀 Setting up Gazeta Internetowa project..."

# Check if we're running in Docker
if [ -f /.dockerenv ] || [ -f /proc/1/cgroup ] && grep -q docker /proc/1/cgroup; then
    echo "🐳 Detected Docker environment"
    DOCKER_MODE=true
else
    echo "💻 Detected local environment"
    DOCKER_MODE=false
fi

if [ "$DOCKER_MODE" = false ]; then
    # Check if .env.local exists (only for local setup)
    if [ ! -f .env.local ]; then
        echo "📝 Creating .env.local from .env..."
        cp .env .env.local
        echo "⚠️  Please edit .env.local with your database credentials before continuing"
        echo "   DATABASE_URL=\"mysql://username:password@localhost:3306/gazeta_internetowa\""
        read -p "Press Enter when you've configured .env.local..."
    fi
fi

echo "🗄️  Setting up database..."

# Create database if it doesn't exist
php bin/console doctrine:database:create --if-not-exists

# Run migrations
echo "📊 Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Load fixtures
echo "👥 Loading fixtures (creating admin user and sample data)..."
php bin/console doctrine:fixtures:load --no-interaction

echo "🧹 Clearing cache..."
php bin/console cache:clear

echo "✅ Setup complete!"
echo ""
echo "🎉 Your application is ready!"
echo "📱 Admin login: admin@example.com / admin123"
echo "👤 Regular user: user@example.com / password123"

if [ "$DOCKER_MODE" = true ]; then
    echo "🌐 Access the application at: http://localhost:8000"
else
    echo "🌐 Start the server: symfony server:start"
fi

echo ""
echo "📚 For more information, see README.md" 