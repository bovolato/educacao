#!/bin/sh
set -e

# Volume ./src montado em /var/www: garantir pastas graváveis pelo pool PHP-FPM (usuário www, UID 1000).
mkdir -p /var/www/storage/logs \
  /var/www/storage/framework/sessions \
  /var/www/storage/framework/views \
  /var/www/storage/framework/cache/data \
  /var/www/bootstrap/cache

chown -R www:www /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

exec docker-php-entrypoint "$@"
