# Docker Setup Guide

## Quick Start (One Command)

```bash
# 1. Copy environment file
cp .env.docker .env

# 2. Generate APP_KEY (edit .env and replace the placeholder)
# Or run: docker compose run --rm app php artisan key:generate --show
# Then paste the output into .env

# 3. Start all services
docker compose up -d

# 4. Run migrations and seed database (first time only)
docker compose exec app php artisan migrate --seed

# 5. Access the application
# Open http://localhost:8080
```

## Architecture

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Browser   │────▶│    Nginx    │────▶│   PHP-FPM   │
│             │     │   :8080     │     │   :9000     │
└─────────────┘     └─────────────┘     └──────┬──────┘
                                               │
                                        ┌──────▼──────┐
                                        │  PostgreSQL │
                                        │    :5432    │
                                        └─────────────┘
```

| Service | Container | Port | Description |
|---------|-----------|------|-------------|
| nginx | tu_nginx | 8080 | Web server |
| app | tu_app | 9000 | PHP-FPM |
| db | tu_db | 5433 | PostgreSQL |

## Common Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# View logs
docker compose logs -f

# Run artisan commands
docker compose exec app php artisan <command>

# Access database
docker compose exec db psql -U tu_admin -d tu_sd_system

# Rebuild after code changes
docker compose build --no-cache app
docker compose up -d
```

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Port 8080 in use | Change `APP_PORT` in `.env` |
| Database connection failed | Wait 30s for db healthcheck, then retry |
| Permission denied on storage | Run `docker compose exec app chmod -R 775 storage` |
| Class not found | Run `docker compose exec app composer dump-autoload` |
| Frontend assets missing | Rebuild: `docker compose build --no-cache` |

## Test User Credentials

| Role | Email | Password |
|------|-------|----------|
| System Admin | admin@tu.test | password |
| Bendahara | bendahara@tu.test | password |
| Petugas | petugas@tu.test | password |

## Production Notes

1. **Change default passwords** in `.env`
2. **Generate new APP_KEY** before deployment
3. **Use HTTPS** with a reverse proxy in production
4. **Enable MIGRATE_ON_START=true** for auto-migrations
