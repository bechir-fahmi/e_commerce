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
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Yarn
RUN npm install -g yarn

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app files and .env
COPY . /var/www/
COPY .env /var/www/.env

# Enable debug mode for Docker testing
RUN sed -i 's/APP_DEBUG=false/APP_DEBUG=true/' .env
RUN sed -i 's/APP_ENV=production/APP_ENV=local/' .env

# Git settings
RUN git config --global url."https://github.com/".insteadOf git@github.com:
RUN git config --global url."https://".insteadOf git://

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --ignore-platform-reqs --no-scripts

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/bootstrap/cache

# Generate Laravel key
RUN php artisan key:generate

# Install JS deps and build
RUN yarn install
RUN yarn build

# Apache config
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/apache2.conf
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]
