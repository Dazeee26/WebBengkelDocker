FROM php:8.2-apache

# Set the document root for Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# INI-Files
# Ensure opcache.ini and xdebug.ini are in the same directory as the Dockerfile
COPY ./opcache.ini "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini"
COPY ./xdebug.ini "$PHP_INI_DIR/conf.d/99-xdebug.ini"
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install PHP Extensions and apt packages
# Add nodejs and npm as you likely need them for frontend assets
RUN apt-get -y update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    git \
    nodejs \
    npm \
    --no-install-recommends && rm -rf /var/lib/apt/lists/* # Cleanup apt cache

RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gd '--with-jpeg' '--with-freetype'
RUN docker-php-ext-install intl opcache pdo_mysql zip gd
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN a2enmod rewrite
COPY package.json package-lock.json ./
# INSTALL APCU
RUN pecl install apcu-5.1.24 && docker-php-ext-enable apcu
RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini
RUN echo "apc.enable=1" >> /usr/local/etc/php/php.ini
# APCU

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- Application Code Copy and Dependency Installation ---
# Ensure .dockerignore is not blocking composer.json, package.json etc.

# Set working directory to where the application code will reside
WORKDIR /var/www/html

# Copy composer files first to leverage caching for composer install
# If composer.json/lock change, only this layer and subsequent ones rebuild
COPY composer.json composer.lock ./

# Run composer install
# This step is critical. If composer.json is not found here, it's a COPY issue.
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy package.json and package-lock.json for NPM dependencies
COPY package.json package-lock.json ./

# Install NPM dependencies and build frontend assets
RUN npm install
RUN npm run build || true # Use '|| true' to allow build to fail without stopping the Docker build, adjust if strict error is needed

# Copy the rest of the application code
# This should be done after composer and npm installs to benefit from build caching
# and ensure all dependencies are in place before the full app code is added.
COPY . .

# Expose port (usually Apache runs on port 80)
EXPOSE 80

# Command to run Apache (default for php-apache image)
CMD ["apache2-foreground"]