#!/bin/sh
set -eu

mkdir -p /var/www/html/var/cache /var/www/html/var/logs
chown -R www-data:www-data /var/www/html/var
chmod -R u+rwX,g+rwX /var/www/html/var

exec /usr/local/bin/docker-php-entrypoint php-fpm
