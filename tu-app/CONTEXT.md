# TU App Local-First Deployment Context

## Proyek
**TU App** - Sistem Tata Usaha Sekolah
- Stack: Laravel 10 + Docker + Nginx + PostgreSQL 15
- PHP: 8.2-fpm-alpine

## Status Saat Ini ✅

| Container | Status | Port |
|-----------|--------|------|
| tu_app | Running | 9000 (internal) |
| tu_nginx | Running | 80 → localhost |
| tu_db | Running | 5432 (internal) |

**Akses**: http://localhost:8080

## Fokus Utama 🎯

1. **Stabilkan aplikasi di LOCAL terlebih dahulu**
2. Perbaiki bug kecil (routing, button, fungsi)
3. **TIDAK** deploy ke VPS/domain dulu

## Workflow Development

```
Edit code → Save → Refresh browser
```

Jika perubahan tidak terbaca:
```bash
docker compose exec tu_app php artisan optimize:clear
```

Rebuild setelah perubahan besar:
```bash
./setup.sh rebuild
```

## Distribusi Aplikasi

- **Method**: ZIP file (bukan Git untuk end user)
- **Dokumentasi**: README.md disertakan sebagai panduan
- **Target user**: Staff TU sekolah

## Branch Structure

| Branch | Fungsi |
|--------|--------|
| `main` | Development lokal |
| `production` | Deployment ke server (nanti) |

## Target Akhir

- [x] Docker stack berjalan normal
- [ ] Aplikasi stabil di local (bug-free)
- [ ] README.md lengkap untuk distribusi ZIP
- [ ] Siap dipindahkan ke server (tahap berikutnya)

## Quick Commands

```bash
# Start
./setup.sh start

# Stop  
./setup.sh stop

# Logs
./setup.sh logs

# Clear cache
docker compose exec tu_app php artisan optimize:clear

# Rebuild (setelah code changes)
./setup.sh rebuild
```

## Test Accounts

| Role | Email | Password |
|------|-------|----------|
| System Admin | admin@tusd.test | password |
| Bendahara | bendahara@tusd.test | password |
| Petugas | petugas@tusd.test | password |
