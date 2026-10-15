#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  RACKSEPHNOX · Stop All Services
# ═══════════════════════════════════════════════════════════════

PID_DIR="/root/.racksephnox-pids"

echo "🛑 Stopping Racksephnox services..."

if [ -d "$PID_DIR" ]; then
    for pid_file in "$PID_DIR"/*.pid; do
        [ -f "$pid_file" ] || continue
        PID=$(cat "$pid_file")
        if ps -p "$PID" >/dev/null 2>&1; then
            kill "$PID" 2>/dev/null || true
            echo "   · Stopped PID $PID"
        fi
        rm -f "$pid_file"
    done
fi

pkill -9 -f "php artisan serve" 2>/dev/null || true
pkill -9 -f "ngrok start"        2>/dev/null || true
pkill -9 -f "ngrok http"         2>/dev/null || true

echo "✅ All Racksephnox services stopped."
