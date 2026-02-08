# TU App Go-Live Roadmap (Detailed)

This document outlines the step-by-step process to transition the TU App from development to a production "Go Live" environment.

## Phase A: Application Hardening (Before Deployment)

The goal here is to ensure the app is secure and robust before real users touch it.

1.  **Strict Validation**
    *   Ensure all forms (Creating students, payments, etc.) have strict validation rules.
    *   Prevent negative payments, duplicate entries, etc.
2.  **Security Headers & Rate Limiting**
    *   Configure Nginx (via Docker) to add security headers (`X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`).
    *   Implement Laravel Rate Limiting on API endpoints to prevent abuse.
3.  **Error Handling**
    *   Customize error pages (404, 500) so users don't see raw stack traces in production.
    *   Ensure `APP_DEBUG=false` hides sensitive info.

## Phase B: Infrastructure Setup

We need a server to host the Docker containers.

1.  **Select a Provider**
    *   **Recommended**: DigitalOcean Droplet or IDCloudHost VPS (Ubuntu 22.04/24.04).
    *   **Spec**: Minimum 2GB RAM, 1 vCPU (2 vCPU recommended).
2.  **Domain Name**
    *   Register a domain (e.g., `sistemtu-nurulfalah.com` or a subdomain `tu.nurulfalah.sch.id`).
    *   Point DNS A Record to the VPS IP address.
3.  **Server Preparation**
    *   SSH into the server.
    *   Install Docker & Docker Compose.
    *   Setup a firewall (UFW) allowing ports 22 (SSH), 80 (HTTP), 443 (HTTPS).

## Phase C: Deployment Pipeline

How do we get code from your laptop to the server?

1.  **GitHub Repository**
    *   Ensure all code is pushed to `main` branch.
2.  **Clone on Server**
    *   `git clone https://github.com/bennyakbar/aplikasi-mi.git` on the VPS.
3.  **Production Environment**
    *   Copy `.env.example` to `.env`.
    *   Set `APP_ENV=production`, `APP_DEBUG=false`.
    *   Set a strong `APP_KEY`.
    *   Configure `APP_URL=https://your-domain.com`.

## Phase D: Data Migration (The "Go Live" Moment)

Moving from "Test Data" to "Real Data".

1.  **Clean Slate**
    *   Run `php artisan migrate:fresh` (wipes all test data).
    *   **Do NOT run seeders** for students/payments (only run seeders for Roles & Permissions).
2.  **Master Data Import**
    *   Import Students (Excel/CSV upload feature - needs to be robust).
    *   Setup initial "Chart of Accounts" (Kode Akun) tailored to the school.
    *   Input "Saldo Awal" (Opening Balances) for cash/bank accounts.
3.  **Create Real Users**
    *   Create the real admin, bendahara, and yayasan accounts.
    *   Force password changes on first login.

## Phase E: Post-Deployment

1.  **SSL/HTTPS**
    *   Use Certbot (Let's Encrypt) to auto-generate SSL certificates for Nginx.
2.  **Backup Automation**
    *   Configure a cron job to run `php artisan backup:run` daily.
    *   Send backups to an external storage (Google Drive/S3) or email.

## Checklist for Approval

- [ ] All "FIXME" and "TODO" comments resolved?
- [ ] Application loads under 2 seconds?
- [ ] Mobile view (responsive) verified for "Petugas Transaksi"?
- [ ] Receipt printing works on school printers?

---

**Next Immediate Steps:**
1. Complete "Phase 6.2 - Input Validation" (ensure forms are unbreakable).
2. Decide on VPS Provider (DigitalOcean / IDCloudHost / etc.).
