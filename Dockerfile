# --- Stage 1: build frontend assets (Vite) ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Stage 2: PHP app ---
FROM php:8.2-fpm

# Cài đặt các thư viện hệ thống và PHP extension cần thiết
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Lấy frontend đã build từ stage 1
COPY --from=assets /app/public/build /var/www/public/build

# Cấp quyền cho thư mục storage và cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

# Render đặt biến PORT tự động, nếu không có thì fallback về 80
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-80}
