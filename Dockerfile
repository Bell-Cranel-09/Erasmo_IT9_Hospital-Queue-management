FROM php:8.4-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Render port
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# Laravel public root
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Allow .htaccess
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy package files first
COPY package*.json ./

RUN npm install

# Copy rest of app
COPY . .

# Build Vite
RUN npm run build

# Laravel setup
RUN php artisan storage:link || true

RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    public/uploads \
 && chown -R www-data:www-data storage bootstrap/cache public/uploads \
 && chmod -R 775 storage bootstrap/cache public/uploads

EXPOSE 10000

CMD ["apache2-foreground"]