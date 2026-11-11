#!/bin/sh
set -eu

cd /var/www/html

: "${APP_KEY:?Set APP_KEY in AutoDev environment variables}"
: "${ADMIN_INITIAL_PASSWORD:?Set ADMIN_INITIAL_PASSWORD in AutoDev environment variables}"
: "${DB_HOST:?Set the MySQL service before starting the app}"

case "${APP_URL:-}" in
    https://*) ;;
    *) echo "APP_URL must be the public HTTPS domain assigned by AutoDev." >&2; exit 1 ;;
esac

case "${MEMBERSHIP_VERIFICATION_BASE_URL:-}" in
    https://*) ;;
    *) echo "MEMBERSHIP_VERIFICATION_BASE_URL must use the public HTTPS domain." >&2; exit 1 ;;
esac

php artisan config:clear --ansi
php artisan storage:link --force --ansi
php artisan migrate --force --ansi
php artisan db:seed --class=InitialAdminSeeder --force --ansi

chown -R www-data:www-data storage bootstrap/cache
exec "$@"
