FROM php:8.2-cli

WORKDIR /var/www/html

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y \
    zip unzip curl git nodejs npm libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install JS dependencies and build frontend
RUN npm install && npm run build

# Set correct permissions for Laravel
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080

# Run migrations, then start server on Render's assigned port
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
