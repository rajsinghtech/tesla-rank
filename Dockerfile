# Use Laravel Sail's PHP image with Composer
FROM laravelsail/php82-composer:latest

# Arguments for build-time configuration with default values
ARG WWWGROUP=1000
ARG NODE_VERSION=20

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs

# Add a non-root user and switch to it
RUN groupadd -g $WWWGROUP sail && \
    useradd -m -g sail sail

USER sail

# Copy composer files, artisan, and bootstrap directory before running composer install
COPY --chown=sail:sail composer.json composer.lock artisan bootstrap/ ./

# Ensure artisan has execute permissions
RUN chmod +x artisan

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the rest of the application code
COPY --chown=sail:sail . .

# Install npm dependencies and build assets
RUN npm install && npm run production

# Switch back to root to set permissions
USER root

# Set permissions for storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]