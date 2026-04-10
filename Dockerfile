# Stage 1: Install PHP vendor (needed by Vite for Ziggy)
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --no-scripts

# Stage 2: Build frontend assets
FROM node:20-alpine AS frontend

WORKDIR /app
COPY package*.json ./
RUN npm ci --prefer-offline
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    git \
    unzip \
    netcat-openbsd \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    icu-data-full \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    autoconf \
    g++ \
    make

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl gd bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN cp .env.example .env || true

RUN php artisan key:generate --force || true

RUN php artisan storage:link || true

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf

COPY <<'EOF' /etc/supervisord.conf
[supervisord]
nodaemon=true
logfile=/dev/null
logfile_maxbytes=0

[program:php-fpm]
command=php-fpm -F
priority=10
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
priority=20
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:queue]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
priority=30
autorestart=true
user=www-data
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

COPY <<'EOF' /entrypoint.sh
#!/bin/sh
php artisan migrate --force || echo "[WARN] migrate failed, continuing"
php artisan config:clear || true
php artisan db:seed --force || echo "[WARN] seed failed, continuing"
exec /usr/bin/supervisord -c /etc/supervisord.conf
EOF

RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
