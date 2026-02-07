# Sistem Informasi Tata Usaha Sekolah Dasar (Web App Lokal)

Dokumen ini menyatukan semua tahap pengembangan: Proposal, Design Sistem, Spesifikasi Teknis & Task Breakdown.

---

## 1. Proposal Awal
### 1.1 Latar Belakang
Administrasi TU SD masih banyak manual atau semi-digital. Data sering ganda, arsip hilang, rekap laporan lambat.

Tujuan: membangun **sistem informasi TU berbasis Web Lokal**, multi-user LAN, aman, mudah dirawat.

### 1.2 Tujuan Proposal
- Menyusun dasar pengembangan sistem TU SD
- Berbasis kasus nyata, bukan asumsi
- Proposal realistis dan bertahap

### 1.3 Ruang Lingkup
- 1 sekolah (MI), extensible ke RA nanti
- Modul inti: data siswa, guru/pegawai, surat, keuangan, role & hak akses

### 1.4 Permasalahan Nyata / Kasus Terkumpul
- Kategori siswa & perbedaan iuran
- Risiko transaksi ganda, kwitansi
- Pemisahan tugas TU tidak jelas
- Monitoring pembayaran lambat
- Monitoring manajemen (dashboard bendahara & yayasan)
- Akuntansi tidak standar
- Pembayaran parsial, sisa kewajiban
- Perubahan kategori di tengah periode, pembayaran kolektif, koreksi terbatas, tunggakan lintas periode
- Basis tahun ajaran

### 1.5 Metodologi Pengumpulan Case
- Observasi langsung
- Wawancara terstruktur
- Case Sheet: kode, deskripsi, dampak, cara saat ini, frekuensi, pihak terdampak

### 1.6 Output Proposal Awal
- Dokumen daftar kasus nyata TU SD
- Klasifikasi kasus
- Prioritas masalah untuk sistem

---

## 2. Design Sistem
### 2.1 Arsitektur Sistem
```
[PC TU 1] \        
[PC TU 2]  --> Browser --> [PC Server Lokal] --> [Database]
[PC TU 3] /
PC Server Lokal:
- Web App (Laravel)
- Web Server (Apache/Nginx)
- Database (MySQL/PostgreSQL)
```

### 2.2 Model Data & Relasi
- `school_unit`, `students`, `student_category`, `fees`, `payments`, `users`, `roles`, `journals`, `transactions_audit`
- Relasi penting antara students, category, fees, payments, users, roles

### 2.3 Role & Hak Akses
- Admin Master Data: input/update siswa, kategori, tarif
- Petugas Transaksi: input pembayaran, generate kwitansi
- Bendahara: monitoring, approve koreksi, tutup periode
- Yayasan: dashboard real-time & laporan
- System Admin: user/role management, backup, konfigurasi

### 2.4 Alur Transaksi & Pembayaran
- Pembayaran penuh atau parsial
- Kwitansi PDF multi-transaksi, unique transaction ID
- Status siswa otomatis: Belum Bayar / Sebagian / Lunas

### 2.5 Alur Akuntansi
- Jurnal otomatis dari pembayaran
- Buku kas, jurnal umum, laporan pemasukan & neraca

### 2.6 Dashboard
- Bendahara: status pembayaran, tunggakan, rekap kas
- Yayasan: grafik real-time, summary, filter unit

### 2.7 Proteksi & Validasi
- Unique transaction ID
- Validasi duplikasi transaksi
- Partial payment aman
- Audit trail, backup otomatis

### 2.8 Tahun Ajaran
- Semua kewajiban, pembayaran, laporan terkait tahun ajaran

### 2.9 Extensibility
- Struktur `school_unit` siap untuk unit RA atau multi-unit

---

## 3. Spesifikasi Teknis & Task Breakdown
### 3.1 Platform & Teknologi
- Backend: Laravel 10+, PHP 8.2
- Web Server: Apache/Nginx
- Database: MySQL/PostgreSQL
- Frontend: Blade/Bootstrap 5
- PDF: Dompdf / Laravel Snappy
- Auth: Laravel Breeze / Jetstream
- Backup: otomatis & manual, lokal & eksternal

### 3.2 Struktur Database
- Tabel: school_unit, student_category, students, fees, payments, users, roles, journals, transactions_audit

### 3.3 Role & Akses
- Sesuai tabel di Design Sistem

### 3.4 Alur Transaksi & Akuntansi
- Pembayaran penuh/parsial, kwitansi PDF, status otomatis, mencegah duplikasi
- Jurnal otomatis, laporan kas/jurnal umum/neraca

### 3.5 Dashboard
- Bendahara & Yayasan monitoring real-time, grafik, summary

### 3.6 Task Breakdown
**Setup & Environment:** install XAMPP/Laravel, Git, backup, env config

**Master Data Siswa & Kategori:** tabel, form input/update, validasi, role admin

**Kewajiban & Fees:** tabel fees, form input/update, validasi, role admin

**Transaksi Pembayaran:** tabel payments, form input, partial payment, generate PDF, validasi duplikasi, role transaksi

**Akuntansi & Jurnal Otomatis:** tabel journals, auto-generate jurnal, laporan, role bendahara

**Dashboard & Monitoring:** dashboard bendahara & yayasan, role implementasi

**Sistem & Keamanan:** role & hak akses, audit trail, backup, validasi data

**Testing & Deployment:** unit & integrasi testing, UAT, deployment server lokal

### 3.7 Timeline (Estimasi)
- Minggu 1–2: Setup environment & master data
- Minggu 3: Implementasi fees & transaksi
- Minggu 4: Jurnal & laporan
- Minggu 5: Dashboard & monitoring
- Minggu 6: Testing & review
- Minggu 7: Deployment & training TU

---

**Semua dokumen ini dapat digunakan langsung untuk implementasi dan pengembangan Sistem Informasi TU SD Web Lokal, siap untuk MI dan extensible ke RA di masa depan.**

