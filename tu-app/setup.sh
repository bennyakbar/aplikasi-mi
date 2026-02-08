#!/bin/bash
# ============================================================================
# TU App Docker Setup Script
# Handles complete setup from fresh start to running application
# ============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "============================================"
echo "   TU App Docker Setup"
echo "============================================"
echo -e "${NC}"

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Error: Docker is not running. Please start Docker first.${NC}"
    exit 1
fi

# Function to show usage
show_usage() {
    echo "Usage: $0 [COMMAND]"
    echo ""
    echo "Commands:"
    echo "  setup     - First-time setup (migrations + seeding)"
    echo "  start     - Start the application"
    echo "  stop      - Stop the application"
    echo "  restart   - Restart the application"
    echo "  rebuild   - Rebuild and restart (use after code changes)"
    echo "  logs      - Show application logs"
    echo "  status    - Show container status"
    echo "  reset     - Reset everything (WARNING: deletes database!)"
    echo "  help      - Show this help message"
    echo ""
}

# Function to setup environment
setup_env() {
    if [ ! -f ".env" ]; then
        echo -e "${YELLOW}Creating .env file from .env.docker...${NC}"
        cp .env.docker .env
        
        # Generate APP_KEY if using default placeholder
        if grep -q "GENERATE_NEW_KEY" .env; then
            echo -e "${YELLOW}Generating APP_KEY...${NC}"
            APP_KEY=$(docker run --rm -v "$(pwd)":/app -w /app php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32));")
            if [ -n "$APP_KEY" ]; then
                sed -i "s|base64:GENERATE_NEW_KEY_WITH_artisan_key_generate|$APP_KEY|g" .env
                echo -e "${GREEN}✅ APP_KEY generated successfully${NC}"
            fi
        fi
    else
        echo -e "${GREEN}✅ .env file already exists${NC}"
    fi
}

# Function to fix permissions
fix_permissions() {
    echo -e "${YELLOW}Fixing storage permissions...${NC}"
    mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
    chmod -R 777 storage bootstrap/cache 2>/dev/null || true
    echo -e "${GREEN}✅ Permissions fixed${NC}"
}

# Function for first-time setup
setup() {
    echo -e "${BLUE}Running first-time setup...${NC}"
    
    setup_env
    fix_permissions
    
    # Remove old volumes to ensure fresh start
    echo -e "${YELLOW}Cleaning old volumes...${NC}"
    docker compose down -v 2>/dev/null || true
    
    # Build and start
    echo -e "${YELLOW}Building containers...${NC}"
    docker compose build --no-cache
    
    echo -e "${YELLOW}Starting containers...${NC}"
    docker compose up -d
    
    # Wait for database
    echo -e "${YELLOW}Waiting for database to be ready...${NC}"
    sleep 10
    
    # Run migrations and seed
    echo -e "${YELLOW}Running migrations...${NC}"
    docker compose exec app php artisan migrate --seed --force
    
    echo -e "${GREEN}"
    echo "============================================"
    echo "   ✅ Setup Complete!"
    echo "============================================"
    echo -e "${NC}"
    echo ""
    echo "🌐 Access the application at: http://localhost:8080"
    echo ""
    echo "📧 Test accounts:"
    echo "   - admin@tusd.test / password (System Admin)"
    echo "   - bendahara@tusd.test / password (Bendahara)"
    echo "   - petugas@tusd.test / password (Petugas Transaksi)"
    echo ""
}

# Function to start containers
start() {
    setup_env
    fix_permissions
    echo -e "${YELLOW}Starting containers...${NC}"
    docker compose up -d
    echo -e "${GREEN}✅ Application started at http://localhost:8080${NC}"
}

# Function to stop containers
stop() {
    echo -e "${YELLOW}Stopping containers...${NC}"
    docker compose down
    echo -e "${GREEN}✅ Application stopped${NC}"
}

# Function to restart containers
restart() {
    echo -e "${YELLOW}Restarting containers...${NC}"
    docker compose restart
    echo -e "${GREEN}✅ Application restarted${NC}"
}

# Function to rebuild
rebuild() {
    echo -e "${YELLOW}Rebuilding application...${NC}"
    fix_permissions
    
    # Build frontend assets on host if npm is available
    if command -v npm &> /dev/null; then
        echo -e "${YELLOW}Building frontend assets...${NC}"
        npm run build
    fi
    
    # Remove public assets volume and rebuild
    docker compose down
    docker volume rm tu-app_public_assets 2>/dev/null || true
    docker compose build --no-cache app
    docker compose up -d
    
    echo -e "${GREEN}✅ Application rebuilt and started${NC}"
}

# Function to show logs
logs() {
    docker compose logs -f
}

# Function to show status
status() {
    echo -e "${BLUE}Container Status:${NC}"
    docker compose ps
    echo ""
    echo -e "${BLUE}Recent App Logs:${NC}"
    docker compose logs app --tail=20
}

# Function to reset everything
reset() {
    echo -e "${RED}⚠️  WARNING: This will delete all data including the database!${NC}"
    read -p "Are you sure? (yes/no): " confirm
    if [ "$confirm" = "yes" ]; then
        echo -e "${YELLOW}Resetting everything...${NC}"
        docker compose down -v
        rm -f .env
        echo -e "${GREEN}✅ Reset complete. Run './setup.sh setup' to start fresh.${NC}"
    else
        echo "Reset cancelled."
    fi
}

# Main command handler
case "${1:-help}" in
    setup)
        setup
        ;;
    start)
        start
        ;;
    stop)
        stop
        ;;
    restart)
        restart
        ;;
    rebuild)
        rebuild
        ;;
    logs)
        logs
        ;;
    status)
        status
        ;;
    reset)
        reset
        ;;
    help|*)
        show_usage
        ;;
esac
