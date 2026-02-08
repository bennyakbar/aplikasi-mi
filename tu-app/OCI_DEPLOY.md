# TU App - OCI Deployment Guide

Complete guide for deploying TU App to Oracle Cloud Infrastructure (OCI) Always Free Tier.

## Prerequisites

| Requirement | Details |
|-------------|---------|
| OCI Account | Always Free Tier with Ampere ARM instance |
| Instance | Ubuntu 22.04/24.04 LTS, 4 vCPU, 24GB RAM, 200GB Storage |
| Domain | Custom domain with DNS access (e.g., `tu.sekolah.sch.id`) |
| SSH Access | SSH key configured for the instance |

---

## Phase 1: Server Preparation

### 1.1 Connect to Your Instance

```bash
ssh -i ~/.ssh/your-key ubuntu@<YOUR_OCI_PUBLIC_IP>
```

### 1.2 Update System

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.3 Install Docker

```bash
# Install Docker
curl -fsSL https://get.docker.com | sudo sh

# Add your user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose plugin
sudo apt install docker-compose-plugin -y

# Logout and login again for group changes
exit
```

### 1.4 Configure Firewall

```bash
# Install and configure UFW
sudo apt install ufw -y
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
sudo ufw status
```

---

## Phase 2: Application Deployment

### 2.1 Clone Repository

```bash
cd ~
git clone https://github.com/bennyakbar/aplikasi-mi.git
cd aplikasi-mi/tu-app
```

### 2.2 Configure Environment

```bash
# Copy environment template
cp .env.docker .env

# Edit environment file
nano .env
```

**Required changes in `.env`:**

```env
APP_NAME="TU Nurul Falah"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu.sekolah.sch.id  # Your actual domain

# Database (change password!)
DB_PASSWORD=YourSecurePasswordHere123!

# Ports for production
APP_PORT=80
```

### 2.3 Generate APP_KEY

```bash
# Generate a secure key
docker run --rm php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32));"

# Copy the output and paste into .env as APP_KEY value
nano .env
```

### 2.4 Initial Deployment

```bash
# Make scripts executable
chmod +x deploy.sh backup.sh setup.sh

# Run full deployment
./deploy.sh deploy
```

---

## Phase 3: SSL Certificate Setup

### 3.1 Point DNS to Server

Before running SSL setup, ensure your domain's DNS A record points to your OCI instance's public IP:

| Type | Name | Value |
|------|------|-------|
| A | tu | YOUR_OCI_PUBLIC_IP |

Wait 5-10 minutes for DNS propagation.

### 3.2 Update Nginx Config

Edit the production nginx config to use your actual domain:

```bash
nano docker/production/nginx.conf
```

Replace `tu.domain.sch.id` with your actual domain (e.g., `tu.sekolah.sch.id`).

### 3.3 Initial HTTP-Only Start

Before getting SSL, start with HTTP only:

```bash
# Create a temporary HTTP-only nginx config
cat > docker/production/nginx-temp.conf << 'EOF'
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php;

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

# Start containers with temp config
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 3.4 Request SSL Certificate

```bash
# Request certificate (replace with your domain and email)
docker compose run --rm certbot certonly \
    --webroot \
    --webroot-path=/var/www/certbot \
    --email admin@sekolah.sch.id \
    --agree-tos \
    --no-eff-email \
    -d tu.sekolah.sch.id
```

### 3.5 Enable HTTPS

```bash
# Restore full HTTPS config
docker compose -f docker-compose.yml -f docker-compose.prod.yml restart nginx
```

### 3.6 Verify SSL

Visit `https://tu.sekolah.sch.id` in your browser. You should see a green padlock.

---

## Phase 4: Database Setup

### 4.1 Run Migrations and Seeders

```bash
# Run migrations (creates tables)
docker compose exec app php artisan migrate --force

# Seed roles and permissions
docker compose exec app php artisan db:seed --class=RoleSeeder --force
docker compose exec app php artisan db:seed --class=AccountSeeder --force

# Create initial admin user
docker compose exec app php artisan db:seed --class=UserSeeder --force
```

### 4.2 Create Production Admin User

```bash
# Access tinker to create a custom admin
docker compose exec app php artisan tinker
```

In Tinker:
```php
$user = new \App\Models\User();
$user->name = 'Administrator';
$user->email = 'admin@sekolah.sch.id';
$user->password = bcrypt('YourSecurePassword123!');
$user->save();
$user->assignRole('System Admin');
exit;
```

---

## Phase 5: Backup Automation

### 5.1 Setup Cron Job

```bash
# Edit crontab
crontab -e

# Add this line (runs backup daily at 2 AM)
0 2 * * * /home/ubuntu/aplikasi-mi/tu-app/backup.sh >> /var/log/tu-backup.log 2>&1
```

### 5.2 Test Backup

```bash
./backup.sh
```

### 5.3 Verify Backups

```bash
ls -la backups/
```

---

## Daily Operations

### Check Status

```bash
./deploy.sh status
```

### View Logs

```bash
./deploy.sh logs
```

### Update Application

```bash
./deploy.sh update
```

### Manual Backup

```bash
./deploy.sh backup
```

### Force SSL Renewal

```bash
./deploy.sh ssl-renew
```

---

## Troubleshooting

### Container Won't Start

```bash
# Check logs
docker compose -f docker-compose.yml -f docker-compose.prod.yml logs app

# Rebuild
docker compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache app
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

### 502 Bad Gateway

```bash
# Check if PHP-FPM is running
docker compose exec app ps aux

# Restart all containers
docker compose -f docker-compose.yml -f docker-compose.prod.yml restart
```

### Permission Denied

```bash
# Fix storage permissions
docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed

```bash
# Check database container
docker compose logs db

# Verify credentials in .env match
docker compose exec db psql -U tu_admin -d tu_sd_system -c "SELECT 1"
```

---

## Security Checklist

- [ ] Changed default `DB_PASSWORD` in `.env`
- [ ] Generated unique `APP_KEY`
- [ ] Set `APP_DEBUG=false`
- [ ] SSL certificate installed and working
- [ ] Firewall configured (only 22, 80, 443 open)
- [ ] Changed default admin password after first login
- [ ] Backup cron job configured and tested

---

## Server Specifications Summary

| Component | Specification |
|-----------|---------------|
| Instance | OCI Ampere A1 (Always Free) |
| OS | Ubuntu 22.04/24.04 LTS |
| CPU | 4 vCPU (ARM64) |
| RAM | 24 GB |
| Storage | 200 GB |
| Docker | Latest |
| PHP | 8.2 (FPM Alpine) |
| PostgreSQL | 15 (Alpine) |
| Nginx | 1.25 (Alpine) |
| SSL | Let's Encrypt (Certbot) |

---

## Support

For issues specific to this deployment:
1. Check container logs: `./deploy.sh logs`
2. Check system resources: `./deploy.sh status`
3. Review this guide for common solutions
