#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  RACKSEPHNOX · Service Status
# ═══════════════════════════════════════════════════════════════

PID_DIR="/root/.racksephnox-pids"
LOG_DIR="/root/racksephnox/storage/logs"

echo "═══════════════════════════════════════════════════════════════"
echo "  RACKSEPHNOX · STATUS"
echo "═══════════════════════════════════════════════════════════════"

# Laravel
if [ -f "$PID_DIR/laravel.pid" ]; then
    PID=$(cat "$PID_DIR/laravel.pid")
    if ps -p "$PID" >/dev/null 2>&1; then
        echo "  ✅ Laravel        RUNNING (PID $PID, port 8000)"
    else
        echo "  ❌ Laravel        STOPPED"
    fi
else
    echo "  ❌ Laravel        NOT STARTED"
fi

# ngrok
if [ -f "$PID_DIR/ngrok.pid" ]; then
    PID=$(cat "$PID_DIR/ngrok.pid")
    if ps -p "$PID" >/dev/null 2>&1; then
        echo "  ✅ ngrok          RUNNING (PID $PID)"
    else
        echo "  ❌ ngrok          STOPPED"
    fi
else
    echo "  ❌ ngrok          NOT STARTED"
fi

# Tunnel URLs
echo ""
echo "  🌐 TUNNELS:"
if curl -s http://127.0.0.1:4040/api/tunnels >/dev/null 2>&1; then
    if command -v jq >/dev/null 2>&1; then
        curl -s http://127.0.0.1:4040/api/tunnels | jq -r '.tunnels[]? | "     · \(.name): \(.public_url)"'
    else
        curl -s http://127.0.0.1:4040/api/tunnels | grep -oE '"public_url":"[^"]+"' | sed 's/"public_url":"/     · /;s/"$//'
    fi
else
    echo "     (ngrok API not reachable)"
fi

# Health
echo ""
echo "  🏥 HEALTH:"
H=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000 2>/dev/null || echo "000")
echo "     Local :8000    HTTP $H"

echo ""
echo "  📝 LOGS:"
echo "     tail -f $LOG_DIR/laravel-8000.log"
echo "     tail -f $LOG_DIR/ngrok.log"
echo "═══════════════════════════════════════════════════════════════"
