FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip

# Install Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Install Yarn
RUN npm install -g yarn

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app files
COPY . /var/www/

# Git config to avoid SSH issues
RUN git config --global url."https://github.com/".insteadOf git@github.com: && \
    git config --global url."https://".insteadOf git://

# Copy or fallback to default .env
RUN cp .env.example .env 2>/dev/null || echo "APP_NAME=FleetCart" > .env

# Install PHP dependencies (IMPORTANT: NO --no-scripts)
RUN composer install --no-dev --no-interaction --ignore-platform-reqs

# Autoload optimization & Laravel setup
RUN composer dump-autoload --optimize && \
    php artisan config:clear || true && \
    php artisan cache:clear || true && \
    php artisan package:discover --ansi || true && \
    php artisan key:generate --no-interaction || true

# Permissions
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage && \
    chmod -R 775 /var/www/bootstrap/cache && \
    mkdir -p /var/www/storage/logs && \
    touch /var/www/storage/logs/laravel.log && \
    chown -R www-data:www-data /var/www/storage/logs && \
    chmod -R 775 /var/www/storage/logs

# Install JS deps and build assets
RUN yarn install && yarn build

# Apache configuration
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/apache2.conf && \
    a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]
