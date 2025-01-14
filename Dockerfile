# Use an official PHP image
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    curl \
    libpq-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        gd \
        mbstring \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        zip \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy project files to the container
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for web server
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/runtime /var/www/html/web/assets

# Expose port 9000
EXPOSE 9000
RUN cp .env.example .env
# Start PHP-FPM server
CMD ["php", "-S", "0.0.0.0:9000", "-t", "web"]
