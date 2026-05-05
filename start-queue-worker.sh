#!/bin/bash
# Racksephnox Queue Worker – runs in background
while true; do
    php artisan queue:work --sleep=3 --tries=3 --max-time=3600
    sleep 1
done
