#!/bin/bash
# ============================================================================
# TU App Production Deployment Script for OCI
# Run this on the OCI server to deploy or update the application
# ============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}"
echo "============================================"
echo "   TU App Production Deployment"
echo "============================================"
echo -e "${NC}"

# Check if running as appropriate user
if [ "$EUID" -eq 0 ]; then
    echo -e "${YELLOW}Warning: Running as root. Consider using a non-root user.${NC}"
fi

# Check if Docker is available
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Error: Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

# Function to show usage
show_usage() {
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  deploy      - Full deployment (pull, build, migrate)"
    echo "  update      - Quick update (pull, restart)"
    echo "  ssl-init    - Initialize SSL certificates (first time only)"
    echo "  ssl-renew   - Force SSL certificate renewal"
    echo "  status      - Show container status"
    echo "  logs        - Show application logs"
    echo "  backup      - Run manual backup"
    echo "  help        - Show this help message"
    echo ""
}

# Function to setup environment
setup_env() {
    if [ ! -f ".env" ]; then
        echo -e "${YELLOW}Creating .env file from .env.docker...${NC}"
        cp .env.docker .env
        
        # Generate APP_KEY
        echo -e "${YELLOW}Generating APP_KEY...${NC}"
        APP_KEY=$(docker run --rm php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32));")
        sed -i "s|base64:GENERATE_NEW_KEY_WITH_artisan_key_generate|$APP_KEY|g" .env
        
        # Set production mode
        sed -i "s|APP_ENV=production|APP_ENV=production|g" .env
        sed -i "s|APP_DEBUG=false|APP_DEBUG=false|g" .env
        
        echo -e "${GREEN}✅ Environment configured${NC}"
        echo -e "${YELLOW}⚠️  Please edit .env to set your domain and database credentials!${NC}"
    fi
}

# Function for full deployment
deploy() {
    echo -e "${BLUE}Starting full deployment...${NC}"
    
    setup_env
    
    # Pull latest code
    echo -e "${YELLOW}Pulling latest code from GitHub...${NC}"
    git pull origin main
    
    # Stop existing containers
    echo -e "${YELLOW}Stopping existing containers...${NC}"
    docker compose -f docker-compose.yml -f docker-compose.prod.yml down
    
    # Build fresh
    echo -e "${YELLOW}Building containers...${NC}"
    docker compose -f docker-compose.yml -f docker-compose.prod.yml build --no-cache
    
    # Start containers
    echo -e "${YELLOW}Starting containers...${NC}"
    docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
    
    # Wait for database
    echo -e "${YELLOW}Waiting for database...${NC}"
    sleep 15
    
    # Run migrations
    echo -e "${YELLOW}Running migrations...${NC}"
    docker compose exec app php artisan migrate --force
    
    # Clear and rebuild caches
    echo -e "${YELLOW}Optimizing application...${NC}"
    docker compose exec app php artisan optimize:clear
    docker compose exec app php artisan config:cache
    docker compose exec app php artisan route:cache
    docker compose exec app php artisan view:cache
    
    echo -e "${GREEN}"
    echo "============================================"
    echo "   ✅ Deployment Complete!"
    echo "============================================"
    echo -e "${NC}"
}

# Function for quick update
update() {
    echo -e "${BLUE}Starting quick update...${NC}"
    
    # Pull latest code
    git pull origin main
    
    # Restart app container only
    docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build app
    
    # Wait and clear caches
    sleep 5
    docker compose exec app php artisan optimize:clear
    docker compose exec app php artisan config:cache
    docker compose exec app php artisan route:cache
    docker compose exec app php artisan view:cache
    
    echo -e "${GREEN}✅ Update complete${NC}"
}

# Function to initialize SSL
ssl_init() {
    echo -e "${BLUE}Initializing SSL certificates...${NC}"
    
    # Check if domain is configured
    DOMAIN=$(grep APP_URL .env | cut -d'/' -f3)
    if [ -z "$DOMAIN" ] || [ "$DOMAIN" = "localhost:8080" ]; then
        echo -e "${RED}Error: Please set APP_URL in .env to your domain (e.g., https://tu.domain.sch.id)${NC}"
        exit 1
    fi
    
    echo -e "${YELLOW}Requesting certificate for: $DOMAIN${NC}"
    
    # Create initial certificate
    docker compose run --rm certbot certonly \
        --webroot \
        --webroot-path=/var/www/certbot \
        --email admin@$DOMAIN \
        --agree-tos \
        --no-eff-email \
        -d $DOMAIN
    
    # Reload nginx
    docker compose exec nginx nginx -s reload
    
    echo -e "${GREEN}✅ SSL certificate installed for $DOMAIN${NC}"
}

# Function to force SSL renewal
ssl_renew() {
    echo -e "${BLUE}Forcing SSL certificate renewal...${NC}"
    docker compose run --rm certbot renew --force-renewal
    docker compose exec nginx nginx -s reload
    echo -e "${GREEN}✅ SSL certificate renewed${NC}"
}

# Function to show status
status() {
    echo -e "${BLUE}Container Status:${NC}"
    docker compose -f docker-compose.yml -f docker-compose.prod.yml ps
    echo ""
    echo -e "${BLUE}Disk Usage:${NC}"
    df -h /
    echo ""
    echo -e "${BLUE}Memory Usage:${NC}"
    free -h
}

# Function to show logs
logs() {
    docker compose -f docker-compose.yml -f docker-compose.prod.yml logs -f --tail=100
}

# Function to run backup
backup() {
    echo -e "${BLUE}Running manual backup...${NC}"
    ./backup.sh
}

# Main command handler
case "${1:-help}" in
    deploy)
        deploy
        ;;
    update)
        update
        ;;
    ssl-init)
        ssl_init
        ;;
    ssl-renew)
        ssl_renew
        ;;
    status)
        status
        ;;
    logs)
        logs
        ;;
    backup)
        backup
        ;;
    help|*)
        show_usage
        ;;
esac
