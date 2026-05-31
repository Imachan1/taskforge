#!/usr/bin/env sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
  echo "APP_KEY is required. Generate it with: php artisan key:generate --show"
  exit 1
fi

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
  mkdir -p "$(dirname "$DB_FILE")"
  touch "$DB_FILE"
fi

php artisan config:clear
php artisan migrate --force

# storage:link is optional for this API-first app; keep non-fatal if already exists.
php artisan storage:link || true

php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
