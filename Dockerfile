FROM php:8.2-apache

# Set environment variable for Apache DocumentRoot (for clarity)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Configure Apache DocumentRoot
# Menggunakan a2enconf dan file .conf yang disalin lebih reliable daripada sed global
# Ini akan dibuat dan disalin di langkah selanjutnya.
# Sementara itu, pastikan konfigurasi default Apache memang menunjuk ke /var/www/html
# dan kita akan menimpanya nanti dengan file konfigurasi kita sendiri.

# Basic Apache configurations
RUN a2enmod rewrite # Penting untuk Laravel (URL rewriting)
# Tambahkan ServerName untuk menghilangkan warning AH00558
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# === PHP Extensions and Tools ===
# INI-Files for PHP
# Pastikan file opcache.ini dan xdebug.ini ada di direktori yang sama dengan Dockerfile ini
COPY ./opcache.ini "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini"
# Jika ini untuk production, Xdebug tidak direkomendasikan. Hapus baris ini untuk production.
# Jika untuk development, biarkan dan pastikan xdebug.ini kamu sesuai.
COPY ./xdebug.ini "$PHP_INI_DIR/conf.d/99-xdebug.ini"
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" # Gunakan php.ini-production untuk production

# Install System Packages and PHP Extensions
# Menggabungkan apt-get update dan install untuk efisiensi caching dan ukuran image
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libicu-dev \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    # Node.js dan NPM untuk frontend (jika kamu build di Dockerfile)
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Configure and Install PHP Extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install -j$(nproc) intl opcache pdo_mysql zip gd

# Install Xdebug (Hanya untuk Development! Hapus untuk Production!)
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install APCu
# Pastikan konfigurasi APCu ditempatkan dengan benar
# Perintah 'docker-php-ext-enable apcu' akan membuat file .ini sendiri, jadi tidak perlu 'echo extension=apcu.so'
RUN pecl install apcu-5.1.24 \
    && docker-php-ext-enable apcu \
    && echo "apc.enable_cli=1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-apcu.ini" \
    && echo "apc.enable=1" >> "$PHP_INI_DIR/conf.d/docker-php-ext-apcu.ini" # Tambahkan ini ke file konfigurasi APCu

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- LARAVEL APPLICATION SETUP ---
# Set working directory to where your application code will reside
# Ini adalah root aplikasi Laravel Anda.
WORKDIR /var/www/html

# Copy composer files first for caching benefits
COPY composer.json composer.lock ./

# Install Composer dependencies (production ready)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Jika Anda memiliki frontend (Vite/Mix/Webpack), bangun di sini
# Node.js sudah diinstal di langkah 'Install Packages'
COPY package.json package-lock.json ./
RUN npm install
RUN npm run build # Ganti dengan perintah build yang sesuai (misal: npm run prod)

# Hapus index.html default dari Apache (jika ada konflik dengan Laravel)
RUN rm -f /var/www/html/index.html

# Copy the rest of your Laravel application code
# Ini adalah langkah KRUSIAL untuk menyalin semua file dan folder (termasuk public/)
COPY . .

# Set proper permissions for Laravel's storage and cache directories
# Apache runs as 'www-data' user in this base image
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Configure Apache Virtual Host
# Pastikan Anda memiliki folder 'docker-compose/apache/' dan file '000-default.conf'
# di root proyek Anda (sama level dengan Dockerfile)
COPY docker-compose/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2ensite 000-default.conf # Mengaktifkan Virtual Host baru ini

# Expose the port Apache is listening on
EXPOSE 80

# Command to run Apache in the foreground
# Ini adalah default CMD untuk image php:x.x-apache
CMD ["apache2-foreground"]