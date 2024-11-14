# Use the official PHP image with FPM
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Node.js and npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Copy existing application directory
COPY . /var/www

# Install PHP dependencies
RUN composer install --prefer-dist --no-dev --no-autoloader

# Install Node dependencies and build assets
RUN npm install
RUN npm run build

# Optimize Composer autoloader
RUN composer dump-autoload --optimize

# Expose port 9000 and start PHP-FPM server
EXPOSE 9000
CMD ["php-fpm"]