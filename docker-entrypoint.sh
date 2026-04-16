#!/bin/bash

# Write .env from Render environment variables
cat > /var/www/html/.env << ENVEOF
APP_NAME=${APP_NAME:-ecommirce1}
APP_ENV=${APP_ENV:-production}
APP_KEY=base64:$(openssl rand -base64 32)
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
FRONTEND_URL=${FRONTEND_URL:-http://localhost:5173}
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-ecommirce1}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}
SESSION_DRIVER=${SESSION_DRIVER:-cookie}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS:-localhost}
ENVEOF

php artisan migrate --force 2>&1 || true
php artisan storage:link 2>&1 || true

exec apache2-foreground
