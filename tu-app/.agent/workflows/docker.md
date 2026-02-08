---
description: Start and run the TU App Docker environment
---

# TU App Docker Workflow

## First-Time Setup (Fresh Install)

// turbo-all

1. Navigate to the tu-app directory:
```bash
cd /home/abusyauqi/nf-sheets/tu-nurulfalah-project/tu-app
```

2. Run the setup script:
```bash
./setup.sh setup
```

This will:
- Create `.env` from template with auto-generated APP_KEY
- Fix all permissions
- Build Docker containers
- Run database migrations and seed users
- Start the application

3. Access the app at http://localhost:8080

## Daily Usage

### Start (if already set up)
```bash
./setup.sh start
```

### Stop
```bash
./setup.sh stop
```

### Check Status
```bash
./setup.sh status
```

### View Logs
```bash
./setup.sh logs
```

## After Code Changes

If you modified PHP, Blade templates, or frontend assets:

```bash
./setup.sh rebuild
```

This rebuilds the Docker image with new code and assets.

## Troubleshooting

### 502 Bad Gateway
```bash
./setup.sh rebuild
```

### Permission Denied
```bash
chmod -R 777 storage bootstrap/cache
```

### Start Fresh
```bash
./setup.sh reset
./setup.sh setup
```

## Test Accounts

| Role | Email | Password |
|------|-------|----------|
| System Admin | admin@tusd.test | password |
| Bendahara | bendahara@tusd.test | password |
| Petugas Transaksi | petugas@tusd.test | password |
