# TU App Docker Setup Guide

## 🚀 Quick Start (One Command)

```bash
./setup.sh setup
```

This single command will:
1. Create `.env` from template
2. Generate a secure `APP_KEY`
3. Fix all permissions
4. Build and start all containers
5. Run database migrations
6. Seed default users

Access the application at **http://localhost:8080**

---

## 📋 Available Commands

| Command | Description |
|---------|-------------|
| `./setup.sh setup` | **First-time setup** - complete installation |
| `./setup.sh start` | Start the application |
| `./setup.sh stop` | Stop the application |
| `./setup.sh restart` | Restart the application |
| `./setup.sh rebuild` | Rebuild after code changes |
| `./setup.sh logs` | View live logs |
| `./setup.sh status` | Check container status |
| `./setup.sh reset` | **Delete everything** and start fresh |

---

## 🏗️ Architecture

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
| nginx | tu_nginx | 8080 (host) → 80 | Web server |
| app | tu_app | 9000 (internal) | PHP-FPM application |
| db | tu_db | 5433 (host) → 5432 | PostgreSQL database |

---

## 👤 Test User Credentials

| Role | Email | Password |
|------|-------|----------|
| System Admin | admin@tusd.test | password |
| Bendahara | bendahara@tusd.test | password |
| Petugas Transaksi | petugas@tusd.test | password |
| Admin Master Data | masterdata@tusd.test | password |
| Yayasan | yayasan@tusd.test | password |

---

## 🔧 Manual Commands (Alternative)

If you prefer running commands manually:

```bash
# 1. Copy and configure environment
cp .env.docker .env
# Edit .env and set APP_KEY (or the setup script does this automatically)

# 2. Fix permissions
chmod -R 777 storage bootstrap/cache

# 3. Build and start
docker compose build
docker compose up -d

# 4. Run migrations (first time only)
docker compose exec app php artisan migrate --seed

# 5. Clear caches if needed
docker compose exec app php artisan optimize:clear
```

---

## 🐛 Troubleshooting

### Common Issues and Solutions

| Issue | Solution |
|-------|----------|
| **502 Bad Gateway** | Run `./setup.sh rebuild` - this fixes stale assets |
| **Permission denied** | Run `chmod -R 777 storage bootstrap/cache` |
| **Database connection failed** | Wait 30 seconds for db healthcheck, then retry |
| **Class not found** | Run `docker compose exec app composer dump-autoload` |
| **Frontend assets missing** | Run `./setup.sh rebuild` |
| **Old CSS/JS being served** | Run `./setup.sh rebuild` to sync assets |

### Check Logs

```bash
# All logs
docker compose logs -f

# Specific service
docker compose logs app --tail=50
docker compose logs nginx --tail=50
docker compose logs db --tail=50
```

### Manual Permission Fix

```bash
# From host machine
chmod -R 777 storage bootstrap/cache

# Or using Docker
docker run --rm -v $(pwd):/app alpine chmod -R 777 /app/storage /app/bootstrap/cache
```

### Complete Reset

If nothing works, do a complete reset:

```bash
./setup.sh reset
./setup.sh setup
```

---

## 🔄 After Code Changes

When you modify PHP code, Blade templates, or frontend assets:

```bash
./setup.sh rebuild
```

This will:
1. Rebuild frontend assets (npm run build)
2. Remove stale asset volumes
3. Rebuild the Docker image
4. Restart all containers

---

## 🚢 Production Deployment

For production deployment:

1. **Generate new APP_KEY**:
   ```bash
   docker compose run --rm app php artisan key:generate --show
   ```
   
2. **Update .env** with:
   - Strong `APP_KEY`
   - Strong `DB_PASSWORD`
   - Correct `APP_URL`
   - `APP_DEBUG=false`
   - `MIGRATE_ON_START=true` (for first deploy only)

3. **Use HTTPS** with a reverse proxy (e.g., Cloudflare, Traefik)

4. **Regular backups** of the database volume

---

## 📁 Volume Information

| Volume | Purpose | Persistence |
|--------|---------|-------------|
| `tu_db_data` | PostgreSQL data | ✅ Persisted |
| `public_assets` | Built frontend assets | Synced from image |
| `./storage` | Laravel storage (logs, cache) | Bind mount to host |
| `./bootstrap/cache` | Laravel config cache | Bind mount to host |
