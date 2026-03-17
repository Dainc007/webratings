#!/bin/bash

echo "🐳 Setting up Laravel Sail Docker Environment"
echo "============================================="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop and try again."
    exit 1
fi

# Backup current .env
if [ -f .env ]; then
    echo "📁 Backing up current .env to .env.valet"
    cp .env .env.valet
fi

# Copy Docker environment
echo "📋 Setting up Docker environment configuration"
cp .env.docker .env

# Create alias for sail command
echo "🚢 Setting up Sail alias"
if ! grep -q "alias sail" ~/.zshrc 2>/dev/null; then
    echo "alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'" >> ~/.zshrc
fi

# Stop any running containers first
echo "🛑 Stopping any existing containers..."
docker compose down 2>/dev/null || true

# Build and start containers
echo "🔨 Building and starting Docker containers..."
docker compose up -d --build

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
docker compose exec mysql mysqladmin ping -h"mysql" --silent --wait=30

# Run migrations
echo "🗄️  Running database migrations..."
if ! docker compose exec laravel.test php artisan migrate --force; then
    echo "⚠️  Standard migration failed. Attempting migrate:fresh (development only)..."
    docker compose exec laravel.test php artisan migrate:fresh --force
fi

# Run seeders
echo "🌱 Seeding database..."
docker compose exec laravel.test php artisan db:seed --force

# Install npm dependencies and build assets
echo "📦 Installing and building frontend assets..."
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run build

# Clear caches
echo "🧹 Clearing application caches..."
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan route:clear
docker compose exec laravel.test php artisan view:clear
docker compose exec laravel.test php artisan optimize:clear

echo ""
echo "✅ Docker environment setup complete!"
echo ""
echo "🌐 Your application is now available at:"
echo "   - Main app: http://localhost:9001"
echo "   - Admin panel: http://localhost:9001/admin"
echo "   - Form Layout Editor: http://localhost:9001/admin/form-layout-editor"
echo ""
echo "📝 Useful commands:"
echo "   - Start containers: docker compose up -d"
echo "   - Stop containers: docker compose down"
echo "   - View logs: docker compose logs"
echo "   - Run artisan: docker compose exec laravel.test php artisan [command]"
echo "   - Run tests: docker compose exec laravel.test php artisan test"
echo "   - SSH into container: docker compose exec laravel.test bash"
echo ""
echo "🔄 To switch back to Valet:"
echo "   1. docker compose down"
echo "   2. cp .env.valet .env"
echo "   3. valet start"
