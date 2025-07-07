FROM php:8.2-apache

# Set the document root for Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# INI-Files
# Pastikan file opcache.ini dan xdebug.ini ada di direktori yang sama dengan Dockerfile
COPY ./opcache.ini "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini"
COPY ./xdebug.ini "$PHP_INI_DIR/conf.d/99-xdebug.ini"
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Install PHP Extensions and apt packages
RUN apt-get -y update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    git \
    nodejs \
    npm # Tambahkan nodejs dan npm untuk menjalankan NVM/frontend build tools

RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gd '--with-jpeg' '--with-freetype'
RUN docker-php-ext-install intl opcache pdo_mysql zip gd
RUN pecl install xdebug && docker-php-ext-enable xdebug # Aktifkan Xdebug setelah instalasi
RUN a2enmod rewrite

# INSTALL APCU
RUN pecl install apcu-5.1.24 && docker-php-ext-enable apcu
# Hindari menimpa file php.ini yang sama berulang kali, gunakan satu perintah
# Gunakan >> untuk append, bukan > untuk overwrite
RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini
RUN echo "apc.enable=1" >> /usr/local/etc/php/php.ini
# APCU

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install NVM and Node.js
# Railway sudah menyediakan Node.js sebagai dependency yang bisa kamu tambahkan jika diperlukan.
# Menginstal NVM di dalam Dockerfile tidak disarankan untuk deployment karena menambah kompleksitas
# dan waktu build. Lebih baik menginstal Node.js langsung atau menggunakan multi-stage build.
# Karena kamu sudah menginstal nodejs dan npm di atas, kamu bisa langsung menggunakan npm/node.
# Jika kamu benar-benar perlu NVM, pertimbangkan untuk menjalankannya di script terpisah atau
# menggunakan base image yang sudah memiliki Node.js.
# Untuk tujuan deployment, NVM jarang digunakan di dalam container.

# Copy application code
# Pastikan semua kode aplikasi kamu berada di direktori yang benar relatif terhadap Dockerfile
# Biasanya, ini adalah langkah terakhir setelah semua dependensi terinstal
COPY . /var/www/html/

# Composer install (jika ada composer.json dan composer.lock)
# Pastikan composer.json dan composer.lock ada di root proyek atau di public
WORKDIR /var/www/html
# Jika composer.json/lock ada di /var/www/html, maka ini benar.
# Jika ada di subdirektori public, sesuaikan WORKDIR atau path COPY.
# COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# NPM install dan build (jika ada package.json dan package-lock.json)
# Pastikan package.json/lock ada di root proyek atau di public
# COPY package.json package-lock.json ./
RUN npm install
RUN npm run build # Jika kamu punya script 'build' di package.json untuk frontend assets

# Expose port (biasanya Apache berjalan di port 80)
EXPOSE 80

# Command to run Apache (default for php-apache image)
CMD ["apache2-foreground"]