#!/bin/bash
# ═══════════════════════════════════════════════════════════════
#  RACKSEPHNOX · ngrok Authtoken Auto-Configuration
#  Port: 8000 · Dual tunnel · Basic Auth
# ═══════════════════════════════════════════════════════════════

set -e

CONFIG_DIR="$HOME/.config/ngrok"
CONFIG_FILE="$CONFIG_DIR/ngrok.yml"

echo "🜂 Racksephnox · ngrok Authtoken Setup"
echo ""

mkdir -p "$CONFIG_DIR"

# ─── Try to detect existing token ───
EXISTING_TOKEN=""

if [ -f "$CONFIG_FILE" ]; then
    TOKEN_LINE=$(grep -E '^\s*authtoken:' "$CONFIG_FILE" 2>/dev/null | head -1 || true)
    if [ -n "$TOKEN_LINE" ]; then
        EXISTING_TOKEN=$(echo "$TOKEN_LINE" | sed 's/.*authtoken:\s*//' | tr -d '"' | tr -d "'" | tr -d ' ')
    fi
fi

# Reject GitHub tokens
if [[ "$EXISTING_TOKEN" == ghp_* ]]; then
    echo "⚠️  Detected GitHub token (ghp_...) — not an ngrok token. Clearing."
    EXISTING_TOKEN=""
fi

# Prompt if empty
if [ -z "$EXISTING_TOKEN" ]; then
    echo ""
    echo "📌 Get your ngrok authtoken from:"
    echo "   https://dashboard.ngrok.com/get-started/your-authtoken"
    echo ""
    read -s -p "Paste your ngrok authtoken: " NEW_TOKEN
    echo ""
    if [ -z "$NEW_TOKEN" ]; then
        echo "❌ No token provided. Aborting."
        exit 1
    fi
    EXISTING_TOKEN="$NEW_TOKEN"
fi

echo "✅ Authtoken: ${EXISTING_TOKEN:0:12}..."

# ─── Write config ───
cat > "$CONFIG_FILE" <<YAML
# ═══════════════════════════════════════════════════════════════
#  RACKSEPHNOX · ngrok Configuration
#  Port 8000 · Dual tunnel · Basic Auth
# ═══════════════════════════════════════════════════════════════
version: "3"

agent:
  authtoken: ${EXISTING_TOKEN}
  log: stdout
  log_format: json
  log_level: info

tunnels:
  # ═══ Primary — Reserved domain ═══
  racksephnox:
    proto: http
    addr: 8000
    domain: unporous-abdul-whirlingly.ngrok-free.dev
    basic_auth:
      - "Racksspiraltech8008:AruEshAru@8778"
    request_header:
      add:
        - "X-Forwarded-Proto: https"
        - "X-Racksephnox-Tunnel: racksephnox"
    response_header:
      add:
        - "X-Frame-Options: SAMEORIGIN"
        - "X-Content-Type-Options: nosniff"
        - "Referrer-Policy: strict-origin-when-cross-origin"
    compression: true
    circuit_breaker: 0.5
    inspect: true

  # ═══ Secondary — Random domain ═══
  spiraltechcosmiclaws:
    proto: http
    addr: 8000
    basic_auth:
      - "Racksspiraltech8008:AruEshAru@8778"
    request_header:
      add:
        - "X-Forwarded-Proto: https"
        - "X-Racksephnox-Tunnel: spiraltechcosmiclaws"
    response_header:
      add:
        - "X-Frame-Options: SAMEORIGIN"
        - "X-Content-Type-Options: nosniff"
    compression: true
    circuit_breaker: 0.5
    inspect: true
YAML

echo ""
echo "🔍 Validating config..."
ngrok config check

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "✅ ngrok configuration complete"
echo "   📄 $CONFIG_FILE"
echo "   🌐 racksephnox         → port 8000 (reserved domain)"
echo "   🌐 spiraltechcosmiclaws → port 8000 (random domain)"
echo "═══════════════════════════════════════════════════════════════"
