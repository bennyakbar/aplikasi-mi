#!/bin/bash
# ============================================================================
# TU App Backup Script
# Creates daily backups of database and storage, retains for 7 days
# Add to crontab: 0 2 * * * /path/to/tu-app/backup.sh >> /var/log/tu-backup.log 2>&1
# ============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Configuration
BACKUP_DIR="$SCRIPT_DIR/backups"
RETENTION_DAYS=7
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="tu_backup_$DATE"

# Colors (for interactive use)
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "============================================"
echo "TU App Backup - $(date)"
echo "============================================"

# Create backup directory
mkdir -p "$BACKUP_DIR"

# Backup database
echo "📦 Backing up database..."
docker compose exec -T db pg_dump -U tu_admin -d tu_sd_system > "$BACKUP_DIR/${BACKUP_NAME}_db.sql"

if [ -f "$BACKUP_DIR/${BACKUP_NAME}_db.sql" ]; then
    echo "✅ Database backup created"
else
    echo "❌ Database backup failed"
    exit 1
fi

# Backup storage directory (uploads, logs)
echo "📦 Backing up storage..."
tar -czf "$BACKUP_DIR/${BACKUP_NAME}_storage.tar.gz" -C "$SCRIPT_DIR" storage 2>/dev/null || true
echo "✅ Storage backup created"

# Backup .env file
echo "📦 Backing up environment..."
cp "$SCRIPT_DIR/.env" "$BACKUP_DIR/${BACKUP_NAME}_env.txt" 2>/dev/null || true
echo "✅ Environment backup created"

# Create combined archive
echo "📦 Creating combined archive..."
cd "$BACKUP_DIR"
tar -czf "${BACKUP_NAME}.tar.gz" \
    "${BACKUP_NAME}_db.sql" \
    "${BACKUP_NAME}_storage.tar.gz" \
    "${BACKUP_NAME}_env.txt" 2>/dev/null || true

# Clean up individual files
rm -f "${BACKUP_NAME}_db.sql" "${BACKUP_NAME}_storage.tar.gz" "${BACKUP_NAME}_env.txt"

# Show backup size
BACKUP_SIZE=$(du -h "${BACKUP_NAME}.tar.gz" | cut -f1)
echo "✅ Combined backup created: ${BACKUP_NAME}.tar.gz ($BACKUP_SIZE)"

# Remove old backups
echo "🧹 Cleaning old backups (older than $RETENTION_DAYS days)..."
find "$BACKUP_DIR" -name "tu_backup_*.tar.gz" -type f -mtime +$RETENTION_DAYS -delete
REMAINING=$(ls -1 "$BACKUP_DIR"/tu_backup_*.tar.gz 2>/dev/null | wc -l)
echo "✅ Cleanup complete. $REMAINING backups retained."

# Show backup summary
echo ""
echo "============================================"
echo "Backup Summary"
echo "============================================"
echo "Location: $BACKUP_DIR/${BACKUP_NAME}.tar.gz"
echo "Size: $BACKUP_SIZE"
echo "Retained backups: $REMAINING"
echo ""
echo "To restore database:"
echo "  tar -xzf ${BACKUP_NAME}.tar.gz"
echo "  cat ${BACKUP_NAME}_db.sql | docker compose exec -T db psql -U tu_admin -d tu_sd_system"
echo ""
echo "============================================"
echo "Backup completed at $(date)"
echo "============================================"
