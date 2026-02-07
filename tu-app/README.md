# How to Run This Application

## Quick Start (Local Development)

### Prerequisites
- PHP 8.1+ with extensions: pgsql, pdo_pgsql, gd, zip, bcmath
- Composer
- Node.js 18+ and npm
- PostgreSQL 14+ (running on port 5433)

### Step-by-Step

```powershell
# 1. Navigate to project
cd tu-app

# 2. Install PHP dependencies (if not done)
php composer.phar install

# 3. Install Node dependencies & build assets
npm install
npm run build

# 4. Copy environment file (already done)
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Configure database in .env
# Edit .env with your PostgreSQL credentials:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5433
# DB_DATABASE=tu_sd_system
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# 7. Create database (in psql or pgAdmin)
# CREATE DATABASE tu_sd_system;

# 8. Run migrations and seed
php artisan migrate --seed

# 9. Start development server
php artisan serve

# 10. Open browser
# http://localhost:8000
```

---

## Test Users (After Seeding)

| Role | Email | Password |
|------|-------|----------|
| System Admin | systemadmin@tu.test | password |
| Bendahara | bendahara@tu.test | password |
| Petugas | petugas@tu.test | password |
| Admin Data | admindata@tu.test | password |
| Yayasan | yayasan@tu.test | password |

---

## Using Docker (Alternative)

If you have Docker installed:

```bash
cd tu-app
cp .env.docker .env
docker compose up -d
docker compose exec app php artisan migrate --seed
# Open http://localhost:8080
```

---

## Common Issues

| Problem | Solution |
|---------|----------|
| Database not found | Create database: `CREATE DATABASE tu_sd_system;` |
| Port 5433 refused | Start PostgreSQL service |
| Class not found | Run `composer dump-autoload` |
| Missing styles | Run `npm run build` |
