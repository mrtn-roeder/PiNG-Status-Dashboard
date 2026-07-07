#!/bin/bash
# --- CONFIGURATION ---
DEVICE="your_device_name"
TOKEN="YOUR_SECRET_TOKEN_HERE"
URL="https://your-domain.tld/heartbeat.php"

# 1. GATHER DATA
TEMP=$(vcgencmd measure_temp | cut -d "=" -f2 | tr -d "'C")
DISK=$(df -h / | awk 'NR==2 {print $5}' | tr -d '%')

# CPU Load: Robust check for various Pi versions
CPU_PCT=$(top -bn1 | grep -i "cpu" | head -n1 | awk '{printf("%.1f", $2 + $4)}' | tr ',' '.')
[ -z "$CPU_PCT" ] && CPU_PCT="0.0"

# RAM Load: Reliable parsing for different OS localizations
MEM_PCT=$(free | awk '/^Mem:/ || /^Speicher:/ {printf("%.1f", $3/$2 * 100.0)}' | tr ',' '.')
[ -z "$MEM_PCT" ] || [ "$MEM_PCT" == "0.0" ] && MEM_PCT=$(free | grep "Mem:" | awk '{printf("%.1f", $3/$2 * 100.0)}' | tr ',' '.')
[ -z "$MEM_PCT" ] && MEM_PCT="0.0"

# Compact load format: "x% / y%"
LAST_STR="${MEM_PCT}% / ${CPU_PCT}%"

# Uptime & Network
UPTIME_SEC=$(cat /proc/uptime | awk '{print int($1)}')
UPTIME_STR="$((UPTIME_SEC/86400))d $((UPTIME_SEC%86400/3600))h $((UPTIME_SEC%3600/60))m"
NET_DEV=$(ip route get 8.8.8.8 2>/dev/null | awk '{print $5}')
NET=$([[ "$NET_DEV" == "wlan"* || "$NET_DEV" == "wl"* ]] && echo "wireless" || echo "ethernet")

# 2. SEND DATA
curl -s -G "$URL" \
    --data-urlencode "token=$TOKEN" \
    --data-urlencode "device=$DEVICE" \
    --data-urlencode "temp=$TEMP" \
    --data-urlencode "disk=$DISK" \
    --data-urlencode "load=$LAST_STR" \
    --data-urlencode "uptime=$UPTIME_STR" \
    --data-urlencode "net=$NET" > /dev/null 2>&1