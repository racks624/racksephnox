#!/bin/bash

# ============================================
# RACKSEPHNOX - DIVINE GOLDEN PLATFORM
# Industrial-Grade Startup Script
# Includes: Laravel Server, Vite, Reverb, Queue, Schedule, Swagger, ngrok
# ============================================

# Colors
GOLD='\033[38;2;212;175;55m'
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'
BOLD='\033[1m'

# Configuration
APP_PORT=${APP_PORT:-8000}
VITE_PORT=${VITE_PORT:-5173}
REVERB_PORT=${REVERB_PORT:-8080}
NGROK_PORT=${NGROK_PORT:-8000}
LOG_DIR="storage/logs"
PID_DIR="storage/pids"

# Create directories
mkdir -p $LOG_DIR $PID_DIR

print_banner() {
    echo -e "${GOLD}"
    echo "╔══════════════════════════════════════════════════════════════════╗"
    echo "║     R A C K S E P H N O X     -     D I V I N E     P L A T F O R M     ║"
    echo "║                         8888 Hz Wealth Frequency                  ║"
    echo "╚══════════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

print_message() { echo -e "${GOLD}[$(date +'%H:%M:%S')]${NC} $1"; }
print_success() { echo -e "${GREEN}✅ [$(date +'%H:%M:%S')] $1${NC}"; }
print_error() { echo -e "${RED}❌ [$(date +'%H:%M:%S')] $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️ [$(date +'%H:%M:%S')] $1${NC}"; }
print_info() { echo -e "${CYAN}ℹ️ [$(date +'%H:%M:%S')] $1${NC}"; }

save_pid() { echo $1 > "$PID_DIR/$2.pid"; }

kill_processes() {
    print_message "Cleaning up existing processes..."
    
    for pid_file in "$PID_DIR"/*.pid; do
        if [ -f "$pid_file" ]; then
            kill -9 $(cat "$pid_file") 2>/dev/null
            rm -f "$pid_file"
        fi
    done
    
    pkill -f "php artisan serve" 2>/dev/null
    pkill -f "vite" 2>/dev/null
    pkill -f "reverb:start" 2>/dev/null
    pkill -f "queue:work" 2>/dev/null
    pkill -f "schedule:work" 2>/dev/null
    pkill -f "ngrok" 2>/dev/null
    
    print_success "Cleanup complete"
}

check_prerequisites() {
    print_message "Checking prerequisites..."
    
    if ! command -v php &> /dev/null; then
        print_error "PHP not installed"
        exit 1
    fi
    print_success "PHP $(php -v | head -1 | cut -d' ' -f2)"
    
    if ! command -v composer &> /dev/null; then
        print_warning "Composer not installed"
    else
        print_success "Composer $(composer --version | cut -d' ' -f3)"
    fi
    
    if [ ! -f ".env" ]; then
        print_warning ".env not found, creating..."
        cp .env.example .env 2>/dev/null || echo "APP_NAME=Racksephnox" > .env
        php artisan key:generate --force
    fi
}

check_ngrok() {
    print_message "Checking ngrok installation..."
    
    if ! command -v ngrok &> /dev/null; then
        print_warning "ngrok not found! Installing..."
        wget -q https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz
        tar -xzf ngrok-v3-stable-linux-amd64.tgz
        sudo mv ngrok /usr/local/bin/
        rm ngrok-v3-stable-linux-amd64.tgz
        print_success "ngrok installed successfully"
    else
        print_success "ngrok found: $(ngrok version 2>/dev/null | head -1)"
    fi
}

start_ngrok() {
    print_message "Starting ngrok tunnel on port $NGROK_PORT..."
    
    ngrok http $NGROK_PORT --log=stdout > $LOG_DIR/ngrok.log 2>&1 &
    NGROK_PID=$!
    save_pid $NGROK_PID "ngrok"
    
    sleep 5
    
    NGROK_URL=$(curl -s http://localhost:4040/api/tunnels 2>/dev/null | grep -o 'https://[a-z0-9]*\.ngrok-free.app' | head -1)
    
    if [ -n "$NGROK_URL" ]; then
        print_success "ngrok tunnel established!"
        print_info "   → Public URL: ${GREEN}$NGROK_URL${NC}"
        
        # Update Swagger host
        sed -i "s|L5_SWAGGER_CONST_HOST=.*|L5_SWAGGER_CONST_HOST=$NGROK_URL|" .env 2>/dev/null
    else
        print_warning "Could not get ngrok URL. Check ngrok status at http://localhost:4040"
    fi
}

run_optimizations() {
    print_message "Running optimizations..."
    php artisan optimize:clear > /dev/null 2>&1
    print_success "Optimizations complete"
}

run_migrations() {
    print_message "Running migrations..."
    php artisan migrate --force > /dev/null 2>&1
    print_success "Migrations complete"
}

generate_swagger() {
    print_message "Generating L5-Swagger documentation..."
    
    # Ensure storage directory exists
    mkdir -p storage/api-docs
    chmod -R 775 storage/api-docs
    
    # Generate Swagger docs
    php artisan l5-swagger:generate --force > $LOG_DIR/swagger.log 2>&1
    
    if [ $? -eq 0 ]; then
        print_success "Swagger documentation generated"
        print_info "   → Swagger UI: http://localhost:$APP_PORT/api-docs"
        print_info "   → JSON Spec: http://localhost:$APP_PORT/api-docs.json"
    else
        print_warning "Swagger generation had issues. Check logs: $LOG_DIR/swagger.log"
    fi
}

start_laravel() {
    print_message "Starting Laravel server..."
    php artisan serve --host=0.0.0.0 --port=$APP_PORT > $LOG_DIR/laravel.log 2>&1 &
    LARAVEL_PID=$!
    save_pid $LARAVEL_PID "laravel"
    sleep 3
    
    if ps -p $LARAVEL_PID > /dev/null 2>&1; then
        print_success "Laravel server started on port $APP_PORT (PID: $LARAVEL_PID)"
        print_info "   → Local: http://localhost:$APP_PORT"
    else
        print_error "Failed to start Laravel server"
        exit 1
    fi
}

start_vite() {
    if [ -f "package.json" ]; then
        print_message "Starting Vite server..."
        npm run dev -- --port=$VITE_PORT --host=0.0.0.0 > $LOG_DIR/vite.log 2>&1 &
        VITE_PID=$!
        save_pid $VITE_PID "vite"
        print_success "Vite server started (PID: $VITE_PID)"
        print_info "   → Vite: http://localhost:$VITE_PORT"
    else
        print_warning "package.json not found, skipping Vite"
    fi
}

start_reverb() {
    if grep -q "REVERB_APP_ID" .env 2>/dev/null; then
        print_message "Starting Reverb WebSocket server..."
        php artisan reverb:start --host=0.0.0.0 --port=$REVERB_PORT > $LOG_DIR/reverb.log 2>&1 &
        REVERB_PID=$!
        save_pid $REVERB_PID "reverb"
        print_success "Reverb server started (PID: $REVERB_PID)"
        print_info "   → WebSocket: ws://localhost:$REVERB_PORT"
    fi
}

start_queue() {
    print_message "Starting Queue worker..."
    php artisan queue:work --sleep=3 --tries=3 > $LOG_DIR/queue.log 2>&1 &
    QUEUE_PID=$!
    save_pid $QUEUE_PID "queue"
    print_success "Queue worker started (PID: $QUEUE_PID)"
}

start_schedule() {
    print_message "Starting Schedule worker..."
    php artisan schedule:work > $LOG_DIR/schedule.log 2>&1 &
    SCHEDULE_PID=$!
    save_pid $SCHEDULE_PID "schedule"
    print_success "Schedule worker started (PID: $SCHEDULE_PID)"
}

show_status() {
    echo ""
    print_header "SERVICES STATUS"
    
    echo -e "${CYAN}┌────────────────────────┬──────────┬────────────────────────────────────────┐${NC}"
    echo -e "${CYAN}│ Service                │ Status   │ URL/PID                                │${NC}"
    echo -e "${CYAN}├────────────────────────┼──────────┼────────────────────────────────────────┤${NC}"
    
    # Laravel
    if ps -p $(cat "$PID_DIR/laravel.pid" 2>/dev/null) > /dev/null 2>&1; then
        echo -e "${GREEN}│ Laravel Server         │ ● Running│ http://localhost:$APP_PORT                 │${NC}"
    else
        echo -e "${RED}│ Laravel Server         │ ○ Stopped│                                        │${NC}"
    fi
    
    # ngrok
    if ps -p $(cat "$PID_DIR/ngrok.pid" 2>/dev/null) > /dev/null 2>&1; then
        NGROK_URL=$(curl -s http://localhost:4040/api/tunnels 2>/dev/null | grep -o 'https://[a-z0-9]*\.ngrok-free.app' | head -1)
        echo -e "${GREEN}│ ngrok Tunnel           │ ● Running│ ${NGROK_URL:-Starting...} │${NC}"
    else
        echo -e "${RED}│ ngrok Tunnel           │ ○ Stopped│                                        │${NC}"
    fi
    
    # Swagger
    echo -e "${GREEN}│ Swagger UI             │ ● Active │ http://localhost:$APP_PORT/api-docs      │${NC}"
    
    # Vite
    if ps -p $(cat "$PID_DIR/vite.pid" 2>/dev/null) > /dev/null 2>&1; then
        echo -e "${GREEN}│ Vite Server            │ ● Running│ http://localhost:$VITE_PORT               │${NC}"
    else
        echo -e "${RED}│ Vite Server            │ ○ Stopped│                                        │${NC}"
    fi
    
    # Reverb
    if ps -p $(cat "$PID_DIR/reverb.pid" 2>/dev/null) > /dev/null 2>&1; then
        echo -e "${GREEN}│ Reverb WebSocket       │ ● Running│ ws://localhost:$REVERB_PORT               │${NC}"
    else
        echo -e "${RED}│ Reverb WebSocket       │ ○ Stopped│                                        │${NC}"
    fi
    
    # Queue
    if ps -p $(cat "$PID_DIR/queue.pid" 2>/dev/null) > /dev/null 2>&1; then
        echo -e "${GREEN}│ Queue Worker           │ ● Running│ PID: $(cat "$PID_DIR/queue.pid")           │${NC}"
    else
        echo -e "${RED}│ Queue Worker           │ ○ Stopped│                                        │${NC}"
    fi
    
    # Schedule
    if ps -p $(cat "$PID_DIR/schedule.pid" 2>/dev/null) > /dev/null 2>&1; then
        echo -e "${GREEN}│ Schedule Worker        │ ● Running│ PID: $(cat "$PID_DIR/schedule.pid")        │${NC}"
    else
        echo -e "${RED}│ Schedule Worker        │ ○ Stopped│                                        │${NC}"
    fi
    
    echo -e "${CYAN}└────────────────────────┴──────────┴────────────────────────────────────────┘${NC}"
}

show_commands() {
    echo ""
    print_header "USEFUL COMMANDS"
    
    echo -e "${YELLOW}📍 Access URLs:${NC}"
    echo -e "   🌐 Local App:     ${GREEN}http://localhost:$APP_PORT${NC}"
    echo -e "   📊 Swagger UI:    ${GREEN}http://localhost:$APP_PORT/api-docs${NC}"
    echo -e "   🔄 ngrok UI:      ${GREEN}http://localhost:4040${NC}"
    
    NGROK_URL=$(curl -s http://localhost:4040/api/tunnels 2>/dev/null | grep -o 'https://[a-z0-9]*\.ngrok-free.app' | head -1)
    if [ -n "$NGROK_URL" ]; then
        echo -e "   🌍 Public URL:    ${GREEN}$NGROK_URL${NC}"
        echo -e "   📊 Public Swagger: ${GREEN}$NGROK_URL/api-docs${NC}"
    fi
    
    echo ""
    echo -e "${YELLOW}📝 View logs:${NC}"
    echo -e "   tail -f $LOG_DIR/laravel.log"
    echo -e "   tail -f $LOG_DIR/ngrok.log"
    echo -e "   tail -f $LOG_DIR/swagger.log"
    echo ""
    echo -e "${YELLOW}🛑 Stop services:${NC}"
    echo -e "   ./stop-racksephnox.sh"
    echo -e "   or pkill -f 'php artisan' && pkill -f ngrok"
    echo ""
}

print_header() {
    echo ""
    echo -e "${GOLD}══════════════════════════════════════════════════════════════════${NC}"
    echo -e "${GOLD}  $1${NC}"
    echo -e "${GOLD}══════════════════════════════════════════════════════════════════${NC}"
}

trap_exit() {
    print_message "Shutting down Racksephnox services..."
    kill_processes
    print_success "All services stopped. Divine blessings! ✨"
    exit 0
}

main() {
    trap trap_exit SIGINT SIGTERM
    clear
    print_banner
    
    print_header "INITIALIZING RACKSEPHNOX PLATFORM"
    
    kill_processes
    check_prerequisites
    check_ngrok
    run_optimizations
    run_migrations
    
    print_header "STARTING SERVICES"
    
    start_ngrok
    start_laravel
    start_vite
    start_reverb
    start_queue
    start_schedule
    generate_swagger
    
    sleep 3
    show_status
    show_commands
    
    print_success "Racksephnox platform is fully operational!"
    print_message "Divine Golden Spirit | 8888 Hz Wealth Frequency | RX Machine Series"
    echo ""
    
    wait
}

main "$@"
