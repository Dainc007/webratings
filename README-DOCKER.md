# Docker Setup for WebRatings

This project can be run using Laravel Sail (Docker) as an alternative to the current Valet setup.

## Why Docker?

The current Valet setup has persistent caching issues that prevent CSS and styling updates from loading properly. Docker provides a clean, isolated environment that can help identify whether the Form Layout Editor styling issues are environment-specific or code-specific.

## Prerequisites

- Docker Desktop installed and running
- Git repository cloned locally

## Quick Setup

1. **Start Docker Desktop**

2. **Run the automated setup**:
   ```bash
   ./setup-docker.sh
   ```

3. **Access your application**:
   - Main app: http://localhost:9001
   - Admin panel: http://localhost:9001/admin
   - Form Layout Editor: http://localhost:9001/admin/form-layout-editor

## Manual Setup

If the automated script fails, you can set up manually:

```bash
# Copy Docker environment configuration
cp .env.docker .env

# Start containers (builds on first run)
./vendor/bin/sail up -d

# Wait for MySQL to be ready, then run migrations
./vendor/bin/sail artisan migrate

# Install and build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

# Clear caches
./vendor/bin/sail artisan optimize:clear
```

## Common Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs

# Run artisan commands
docker-compose exec laravel.test php artisan migrate
docker-compose exec laravel.test php artisan tinker

# Run tests
docker-compose exec laravel.test php artisan test

# SSH into the app container
docker-compose exec laravel.test bash

# Run npm commands
docker-compose exec laravel.test npm install
docker-compose exec laravel.test npm run dev

# Database access
docker-compose exec mysql mysql -u sail -ppassword webratings
```

## Services Included

- **Laravel App** (PHP 8.4 + Nginx) - Port 9001 (configurable via `APP_PORT`)
- **MySQL 8.4** - Port 3306
- **Redis** - Port 6379
- **Vite** - Port 5173

## Environment Differences

### Database
- **Valet**: SQLite (`database/database.sqlite`)
- **Docker**: MySQL (`webratings` database)

### Caching
- **Valet**: File-based cache
- **Docker**: Redis cache and sessions

### URLs
- **Valet**: https://webratings.test
- **Docker**: http://localhost:9001

## Switching Between Environments

### From Valet to Docker
```bash
# Backup current .env
cp .env .env.valet

# Switch to Docker
cp .env.docker .env
docker-compose up -d
```

### From Docker to Valet
```bash
# Stop Docker containers
docker-compose down

# Restore Valet configuration
cp .env.valet .env

# Start Valet
valet start
```

## Troubleshooting

### Docker not running
```bash
# Check Docker status
docker info

# Start Docker Desktop application
```

### Port conflicts
If port 80 is busy, edit `.env` and change `APP_PORT`:
```env
APP_PORT=8080
```

### Cache and Connection Issues
If you encounter a 500 error or "getaddrinfo for mysql failed", it's likely due to a stale configuration cache from the host machine. Run:
```bash
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan route:clear
```

### Why HTTP and not HTTPS?
Laravel Sail runs on HTTP by default for local development. If your browser forces HTTPS, try:
- Clearing browser cache/cookies for localhost
- Using an Incognito/Private window
- Explicitly typing http://localhost:9001

### Common Port Conflicts on macOS
If you see "Connection Reset" or "Connection Refused", another service might be using the port.
- **Port 9000**: Commonly used by PHP-FPM (Valet/Herd).
- **Port 80**: Commonly used by Apache/Nginx.

We changed the default port to **9001** to avoid these conflicts. If you still have issues, you can change `APP_PORT` in `.env`.

### Vite/Rollup Architecture Mismatch (e.g., Missing @rollup/rollup-darwin-arm64)
If you see an error like `Error: Cannot find module @rollup/rollup-darwin-arm64` when running `npm run dev` or `npm run build` on your host:

1. Delete `node_modules` and `package-lock.json` on your host.
2. Run `npm install` on your host (Mac).
3. Run `docker compose exec laravel.test npm install` inside Docker.

This ensures both the macOS and Linux binaries are present in the shared `node_modules`.

### Vite not accessible from host
If you run `npm run dev` inside Docker and can't access it from the browser:
- Ensure `vite.config.js` has `server: { host: '0.0.0.0' }`. (This is already configured in the project).
- Check if port 5173 is exposed in `docker-compose.yml`.

### Fresh start
```bash
# Stop everything and remove volumes
./vendor/bin/sail down -v

# Rebuild and start
./vendor/bin/sail up -d --build

# Re-run migrations
./vendor/bin/sail artisan migrate
```

## Testing the Form Layout Editor

Once Docker is running:

1. Visit http://localhost:9001/admin
2. Log in to Filament admin
3. Navigate to "Form Layout Editor" in the sidebar
4. Test the UI to see if CSS/styling issues persist

This will help determine if the styling problems are:
- **Environment-specific**: Issues only occur in Valet setup
- **Code-specific**: Issues persist in both environments

## Performance Notes

- First startup takes longer (building images, downloading packages)
- Subsequent starts are much faster
- File watching for development works via volume mounts
- Hot reload for Vite works on port 5173
