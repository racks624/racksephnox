#!/bin/bash
# ============================================================
# Racksephnox - Divine Golden Platform
# Production Startup Script
# ============================================================
# Usage: ./racksephnox-prod-start.sh {start|stop|restart|status}
# ============================================================

set -euo pipefail

# ------------------------------
# Colors & Paths
# ------------------------------
GOLD='\033[38;2;212;175;55m'
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

APP_PORT=${APP_PORT:-8000}
LOG_DIR="storage/logs"
PID_DIR="storage/framework/pids"
mkdir -p "$LOG_DIR" "$PID_DIR"

LARAVEL_PID="$PID_DIR/laravel.pid"
VITE_PID="$PID_DIR/vite.pid"
REVERB_PID="$PID_DIR/reverb.pid"
QUEUE_PID="$PID_DIR/queue.pid"
SCHEDULE_PID="$PID_DIR/schedule.pid"

# ------------------------------
# Helper Functions
# ------------------------------
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

banner() {
    echo -e "${GOLD}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║     R A C K S E P H N O X     -     D I V I N E     P L A T F O R M     ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

check_environment() {
    if [ ! -f .env ]; then
        log_error ".env file missing. Run 'cp .env.example .env' and configure."
        exit 1
    fi

    if ! grep -q "^APP_KEY=" .env || [ -z "$(grep "^APP_KEY=" .env | cut -d= -f2)" ]; then
        log_error "APP_KEY not set. Run 'php artisan key:generate'."
        exit 1
    fi

    log_info "Environment check passed."
}

run_migrations() {
    log_info "Running migrations..."
    php artisan migrate --force > "$LOG_DIR/migrate.log" 2>&1
    log_info "Migrations completed."
}

clear_production_caches() {
    log_info "Optimizing for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    log_info "Caches optimized."
}

start_laravel() {
    if [ -f "$LARAVEL_PID" ] && kill -0 "$(cat "$LARAVEL_PID")" 2>/dev/null; then
        log_warn "Laravel server already running (PID $(cat "$LARAVEL_PID"))"
        return 0
    fi
    log_info "Starting Laravel server on port $APP_PORT..."
    php artisan serve --host=0.0.0.0 --port="$APP_PORT" > "$LOG_DIR/laravel.log" 2>&1 &
    echo $! > "$LARAVEL_PID"
    sleep 2
    if kill -0 "$(cat "$LARAVEL_PID")" 2>/dev/null; then
        log_info "Laravel server started (PID $(cat "$LARAVEL_PID"))"
    else
        log_error "Laravel server failed to start. Check $LOG_DIR/laravel.log"
        exit 1
    fi
}

start_vite() {
    if [ -f "package.json" ]; then
        if [ -f "$VITE_PID" ] && kill -0 "$(cat "$VITE_PID")" 2>/dev/null; then
            log_warn "Vite already running (PID $(cat "$VITE_PID"))"
            return 0
        fi
        log_info "Starting Vite dev server..."
        npm run dev -- --host=0.0.0.0 > "$LOG_DIR/vite.log" 2>&1 &
        echo $! > "$VITE_PID"
        log_info "Vite started (PID $(cat "$VITE_PID"))"
    else
        log_warn "package.json not found – skipping Vite."
    fi
}

start_reverb() {
    if grep -q "^REVERB_APP_ID=" .env 2>/dev/null; then
        if [ -f "$REVERB_PID" ] && kill -0 "$(cat "$REVERB_PID")" 2>/dev/null; then
            log_warn "Reverb already running (PID $(cat "$REVERB_PID"))"
            return 0
        fi
        log_info "Starting Laravel Reverb..."
        php artisan reverb:start --host=0.0.0.0 --port=8080 > "$LOG_DIR/reverb.log" 2>&1 &
        echo $! > "$REVERB_PID"
        log_info "Reverb started (PID $(cat "$REVERB_PID"))"
    else
        log_warn "Reverb not configured – skipping."
    fi
}

start_queue() {
    if [ -f "$QUEUE_PID" ] && kill -0 "$(cat "$QUEUE_PID")" 2>/dev/null; then
        log_warn "Queue worker already running (PID $(cat "$QUEUE_PID"))"
        return 0
    fi
    log_info "Starting queue worker..."
    php artisan queue:work --sleep=3 --tries=3 --max-jobs=1000 > "$LOG_DIR/queue.log" 2>&1 &
    echo $! > "$QUEUE_PID"
    log_info "Queue worker started (PID $(cat "$QUEUE_PID"))"
}

start_schedule() {
    if [ -f "$SCHEDULE_PID" ] && kill -0 "$(cat "$SCHEDULE_PID")" 2>/dev/null; then
        log_warn "Schedule worker already running (PID $(cat "$SCHEDULE_PID"))"
        return 0
    fi
    log_info "Starting schedule worker..."
    php artisan schedule:work > "$LOG_DIR/schedule.log" 2>&1 &
    echo $! > "$SCHEDULE_PID"
    log_info "Schedule worker started (PID $(cat "$SCHEDULE_PID"))"
}

stop_process() {
    local pid_file=$1
    local name=$2
    if [ -f "$pid_file" ]; then
        local pid=$(cat "$pid_file")
        if kill -0 "$pid" 2>/dev/null; then
            log_info "Stopping $name (PID $pid)..."
            kill -TERM "$pid" 2>/dev/null || true
            sleep 2
            if kill -0 "$pid" 2>/dev/null; then
                kill -9 "$pid" 2>/dev/null || true
            fi
        fi
        rm -f "$pid_file"
    fi
}

stop_all() {
    log_info "Stopping all Racksephnox processes..."
    stop_process "$LARAVEL_PID" "Laravel"
    stop_process "$VITE_PID" "Vite"
    stop_process "$REVERB_PID" "Reverb"
    stop_process "$QUEUE_PID" "Queue worker"
    stop_process "$SCHEDULE_PID" "Schedule worker"
    # Also kill orphaned processes (fallback)
    pkill -f "php artisan serve" 2>/dev/null || true
    pkill -f "vite" 2>/dev/null || true
    pkill -f "reverb:start" 2>/dev/null || true
    pkill -f "queue:work" 2>/dev/null || true
    pkill -f "schedule:work" 2>/dev/null || true
    log_info "All processes stopped."
}

health_check() {
    local max_attempts=10
    local attempt=1
    while [ $attempt -le $max_attempts ]; do
        if curl -s -f "http://localhost:$APP_PORT/api/health" > /dev/null 2>&1; then
            log_info "Health check passed (HTTP 200)."
            return 0
        fi
        log_warn "Health check attempt $attempt/$max_attempts failed. Waiting 2s..."
        sleep 2
        attempt=$((attempt + 1))
    done
    log_error "Health check failed after $max_attempts attempts."
    return 1
}

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
    echo -e "   🎰 Lottery:     ${GOLD}http://localhost:$APP_PORT/lottery${NC}"
    echo -e "   🤖 Machines:    ${GOLD}http://localhost:$APP_PORT/machines${NC}"
    echo -e "   ₿ Trading:     ${GOLD}http://localhost:$APP_PORT/trading${NC}"
    echo ""
    echo -e "${YELLOW}📝 View logs:${NC}"
    echo -e "   tail -f $LOG_DIR/laravel.log"
    echo -e "   tail -f $LOG_DIR/queue.log"
    echo ""
    echo -e "${YELLOW}🛑 Stop services:${NC}"
    echo -e "   ./racksephnox-prod-start.sh stop"
    echo ""
    echo -e "${GOLD}I Am The Source | Divine Golden Phi | Infinite Spiral of Creation${NC}"
    echo -e "${GOLD}════════════════════════════════════════════════════════════════${NC}"
}

start() {
    banner
    check_environment
    clear_production_caches
    run_migrations
    start_laravel
    start_vite
    start_reverb
    start_queue
    start_schedule
    sleep 3
    if health_check; then
        show_status
    else
        log_error "Health check failed – check logs."
        exit 1
    fi
}

case "${1:-start}" in
    start)
        start
        ;;
    stop)
        stop_all
        ;;
    restart)
        stop_all
        sleep 2
        start
        ;;
    status)
        if [ -f "$LARAVEL_PID" ] && kill -0 "$(cat "$LARAVEL_PID")" 2>/dev/null; then
            echo -e "${GREEN}✅ Laravel running (PID $(cat "$LARAVEL_PID"))${NC}"
        else
            echo -e "${RED}❌ Laravel not running${NC}"
        fi
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status}"
        exit 1
        ;;
esac
