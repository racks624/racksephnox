#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  RACKSEPHNOX · Auto-Start (nohup persistence)
#  · Laravel on port 8000
#  · ngrok tunnels (racksephnox + spiraltechcosmiclaws)
# ═══════════════════════════════════════════════════════════════

PROJECT_DIR="/root/racksephnox"
LOG_DIR="$PROJECT_DIR/storage/logs"
PID_DIR="/root/.racksephnox-pids"

mkdir -p "$LOG_DIR" "$PID_DIR"

echo "🜂 Racksephnox starting at $(date '+%Y-%m-%d %H:%M:%S')"
echo ""

# ─── Stop existing services ───
echo "🛑 Stopping any running services..."
for pid_file in "$PID_DIR"/*.pid; do
    [ -f "$pid_file" ] || continue
    PID=$(cat "$pid_file")
    if ps -p "$PID" >/dev/null 2>&1; then
        kill "$PID" 2>/dev/null || true
        echo "   · Killed PID $PID"
    fi
    rm -f "$pid_file"
done
pkill -9 -f "php artisan serve" 2>/dev/null || true
pkill -9 -f "ngrok start"        2>/dev/null || true
pkill -9 -f "ngrok http"         2>/dev/null || true
sleep 2

# ─── Verify ngrok config ───
if [ ! -f "$HOME/.config/ngrok/ngrok.yml" ]; then
    echo "❌ ngrok config missing. Run scripts/setup-ngrok-auth.sh first."
    exit 1
fi

# ─── Clear Laravel caches ───
cd "$PROJECT_DIR" || exit 1
echo "🧹 Clearing caches..."
php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear  >/dev/null 2>&1 || true
php artisan view:clear   >/dev/null 2>&1 || true

# ─── Start Laravel on port 8000 ───
echo "🚀 Starting Laravel on port 8000..."
nohup php artisan serve --host=0.0.0.0 --port=8000 \
    > "$LOG_DIR/laravel-8000.log" 2>&1 &
LARAVEL_PID=$!
echo "$LARAVEL_PID" > "$PID_DIR/laravel.pid"
sleep 3

if curl -s -o /dev/null -w "%{http_code}" http://localhost:8000 | grep -qE "200|302|401"; then
    echo "   ✅ Laravel responding on port 8000 (PID $LARAVEL_PID)"
else
    echo "   ⚠️  Laravel not responding yet — check $LOG_DIR/laravel-8000.log"
fi

# ─── Start ngrok (both tunnels) ───
echo "🌐 Starting ngrok tunnels..."
nohup ngrok start racksephnox spiraltechcosmiclaws \
    > "$LOG_DIR/ngrok.log" 2>&1 &
NGROK_PID=$!
echo "$NGROK_PID" > "$PID_DIR/ngrok.pid"
sleep 6

# ─── Fetch tunnel URLs ───
if command -v jq >/dev/null 2>&1; then
    URLS=$(curl -s http://127.0.0.1:4040/api/tunnels 2>/dev/null \
           | jq -r '.tunnels[]? | "\(.name): \(.public_url)"' 2>/dev/null || echo "")
else
    URLS=$(curl -s http://127.0.0.1:4040/api/tunnels 2>/dev/null || echo "")
fi

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "  ✅ Racksephnox is LIVE"
echo "═══════════════════════════════════════════════════════════════"
echo "  🏠 Local:  http://localhost:8000"
echo "  🌍 Public: https://unporous-abdul-whirlingly.ngrok-free.dev"
echo ""
if [ -n "$URLS" ]; then
    echo "  📋 Active tunnels:"
    echo "$URLS" | sed 's/^/     /'
    echo ""
fi
echo "  🔐 Auth:   Racksspiraltech8008 / (password set)"
echo ""
echo "  📝 Logs:"
echo "     Laravel:  tail -f $LOG_DIR/laravel-8000.log"
echo "     ngrok:    tail -f $LOG_DIR/ngrok.log"
echo ""
echo "  🛑 Stop:     $PROJECT_DIR/scripts/stop-racksephnox.sh"
echo "  📊 Status:   $PROJECT_DIR/scripts/status-racksephnox.sh"
echo "═══════════════════════════════════════════════════════════════"
