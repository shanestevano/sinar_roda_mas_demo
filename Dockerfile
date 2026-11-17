# ============================================================
# Stage 1: Build Frontend Assets (Vite + Tailwind CSS)
# ============================================================
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# ============================================================
# Stage 2: Production PHP 8.4 Runtime (Alpine + Nginx + FPM)
# ============================================================
FROM php:8.4-fpm-alpine

# Install core utilities and system libraries
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash \
    sed \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu-libs

# Install required PHP extensions via official installer script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    sqlite3 \
    bcmath \
    mbstring \
    zip \
    opcache \
    gd \
    intl \
    pcntl \
    exif

# Install Composer from official docker image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy container configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Normalize line endings (CRLF -> LF) and set execution permissions
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh && \
    chown -R www-data:www-data /var/www/html

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
