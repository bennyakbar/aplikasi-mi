# TU App - Sistem Tata Usaha Sekolah

Aplikasi manajemen keuangan sekolah berbasis Laravel + Docker.

## 🚀 Quick Start (Docker - Recommended)

```bash
cd tu-app
chmod +x setup.sh
./setup.sh start
# Buka http://localhost:8080
```

### Akun Test

| Role | Email | Password |
|------|-------|----------|
| System Admin | admin@tusd.test | password |
| Bendahara | bendahara@tusd.test | password |
| Petugas | petugas@tusd.test | password |

---

## 📦 Fitur Utama

- ✅ Manajemen Data Siswa
- ✅ Pencatatan Pembayaran & Kwitansi (A5 Landscape)
- ✅ Laporan Keuangan (General Ledger, Trial Balance)
- ✅ Multi-role User Management
- ✅ Export PDF & Excel
- ✅ Logo Sekolah di Kwitansi

---

## 🔧 Konfigurasi

### Ganti Logo Sekolah

Letakkan file logo di:
```
public/images/logo.png
```
Ukuran rekomendasi: 200x200 pixel (PNG transparan)

### Ganti Nama & Alamat Sekolah

Edit file: `resources/views/payments/receipt.blade.php`
```html
<p class="school-name">MI NURUL FALAH</p>
<p class="school-address">Jl. Contoh Alamat No. 123, Kota</p>
```

---

## 🐳 Docker Commands

```bash
# Start
./setup.sh start

# Stop
./setup.sh stop

# Rebuild (after code changes)
./setup.sh rebuild

# Logs
./setup.sh logs

# Clear cache
docker compose exec app php artisan optimize:clear
```

---

## 💻 Local Development (Tanpa Docker)

### Prerequisites
- PHP 8.2+ dengan extensions: pgsql, pdo_pgsql, gd, zip, bcmath
- Composer
- Node.js 18+ dan npm
- PostgreSQL 15+

### Setup

```bash
cd tu-app
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
# Edit .env untuk database
php artisan migrate --seed
php artisan serve
# Buka http://localhost:8000
```

---

## 📂 Struktur Folder Penting

```
tu-app/
├── public/images/         # Logo sekolah
├── resources/views/       # Blade templates
├── storage/               # Upload files, logs
├── docker-compose.yml     # Docker config
└── setup.sh               # Docker helper script
```

---

## ❓ Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Container tidak jalan | `./setup.sh rebuild` |
| Perubahan tidak tampil | `docker compose exec app php artisan optimize:clear` |
| Database error | Pastikan `tu_db` container running |
| Logo tidak muncul | Copy ke `public/images/logo.png` lalu restart |
