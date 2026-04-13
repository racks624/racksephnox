#!/bin/bash

# ============================================
# Racksephnox - Divine Golden Platform
# Simplified Startup Script (No Swagger)
# ============================================

# Colors
GOLD='\033[38;2;212;175;55m'
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Configuration
APP_PORT=${APP_PORT:-8000}
LOG_DIR="storage/logs"

# Create log directory
mkdir -p $LOG_DIR

# Print banner
echo -e "${GOLD}"
echo "╔══════════════════════════════════════════════════════════════╗"
echo "║     R A C K S E P H N O X     -     D I V I N E     P L A T F O R M     ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Kill existing processes
kill_processes() {
    echo -e "${YELLOW}🔄 Cleaning up old processes...${NC}"
    pkill -f "php artisan serve" 2>/dev/null
    pkill -f "vite" 2>/dev/null
    pkill -f "reverb:start" 2>/dev/null
    pkill -f "queue:work" 2>/dev/null
    pkill -f "schedule:work" 2>/dev/null
    lsof -ti:$APP_PORT | xargs kill -9 2>/dev/null
    echo -e "${GREEN}✅ Cleanup complete${NC}"
}

# Start Laravel server
start_laravel() {
    echo -e "${GOLD}🚀 Starting Laravel server...${NC}"
    php artisan serve --host=0.0.0.0 --port=$APP_PORT > $LOG_DIR/laravel.log 2>&1 &
    sleep 2
    echo -e "${GREEN}✅ Laravel: http://localhost:$APP_PORT${NC}"
}

# Start Vite (if package.json exists)
start_vite() {
    if [ -f "package.json" ]; then
        echo -e "${GOLD}🎨 Starting Vite...${NC}"
        npm run dev -- --host=0.0.0.0 > $LOG_DIR/vite.log 2>&1 &
        echo -e "${GREEN}✅ Vite: http://localhost:5173${NC}"
    fi
}

# Start Reverb (if configured)
start_reverb() {
    if grep -q "REVERB_APP_ID" .env 2>/dev/null; then
        echo -e "${GOLD}🔊 Starting Reverb...${NC}"
        php artisan reverb:start --host=0.0.0.0 --port=8080 > $LOG_DIR/reverb.log 2>&1 &
        echo -e "${GREEN}✅ Reverb: ws://localhost:8080${NC}"
    fi
}

# Start Queue worker
start_queue() {
    echo -e "${GOLD}📦 Starting Queue worker...${NC}"
    php artisan queue:work --sleep=3 --tries=3 > $LOG_DIR/queue.log 2>&1 &
    echo -e "${GREEN}✅ Queue worker started${NC}"
}

# Start Schedule worker
start_schedule() {
    echo -e "${GOLD}⏰ Starting Schedule worker...${NC}"
    php artisan schedule:work > $LOG_DIR/schedule.log 2>&1 &
    echo -e "${GREEN}✅ Schedule worker started${NC}"
}

# Run migrations
run_migrations() {
    echo -e "${GOLD}📋 Running migrations...${NC}"
    php artisan migrate --force > /dev/null 2>&1
    echo -e "${GREEN}✅ Migrations complete${NC}"
}

# Clear caches
clear_caches() {
    echo -e "${GOLD}🗑️  Clearing caches...${NC}"
    php artisan optimize:clear > /dev/null 2>&1
    echo -e "${GREEN}✅ Caches cleared${NC}"
}

# Show final status
show_status() {
    echo ""
    echo -e "${GOLD}════════════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}✨ Racksephnox is fully operational!${NC}"
    echo -e "${GOLD}════════════════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "${GREEN}📍 Access URLs:${NC}"
    echo -e "   🌐 Main App:  ${GOLD}http://localhost:$APP_PORT${NC}"
    echo -e "   🔐 Register:   ${GOLD}http://localhost:$APP_PORT/register${NC}"
    echo -e "   🔑 Login:      ${GOLD}http://localhost:$APP_PORT/login${NC}"
    echo -e "   📊 Dashboard:  ${GOLD}http://localhost:$APP_PORT/dashboard${NC}"
    echo -e "   👑 Admin:      ${GOLD}http://localhost:$APP_PORT/admin${NC}"
    echo ""
    echo -e "${YELLOW}📝 View logs:${NC}"
    echo -e "   tail -f $LOG_DIR/laravel.log"
    echo ""
    echo -e "${YELLOW}🛑 Stop services:${NC}"
    echo -e "   pkill -f 'php artisan'"
    echo ""
    echo -e "${GOLD}I Am The Source | Divine Golden Phi | Infinite Spiral of Creation${NC}"
    echo -e "${GOLD}════════════════════════════════════════════════════════════════${NC}"
}

# Main execution
main() {
    kill_processes
    clear_caches
    run_migrations
    start_laravel
    start_vite
    start_reverb
    start_queue
    start_schedule
    sleep 3
    show_status
}

# Run main function
main "$@"
