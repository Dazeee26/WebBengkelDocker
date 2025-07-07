# Gunakan image dasar PHP dengan Apache
FROM php:8.2-apache

# --- Konfigurasi Apache dan PHP INI ---

# Atur document root Apache ke direktori 'public' Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
# Modifikasi konfigurasi Apache untuk menggunakan document root yang benar
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Salin file konfigurasi PHP INI kustom (opcache dan xdebug)
# Pastikan file-file ini ada di direktori root proyek Anda (di samping Dockerfile)
COPY ./opcache.ini "$PHP_INI_DIR/conf.d/docker-php-ext-opcache.ini"
COPY ./xdebug.ini "$PHP_INI_DIR/conf.d/99-xdebug.ini"
# Ganti php.ini-development dengan php.ini untuk konfigurasi default yang lebih cocok untuk dev/prod
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# --- Instalasi Dependensi Sistem dan Ekstensi PHP ---

# Perbarui daftar paket dan instal dependensi yang diperlukan
# Termasuk libicu-dev untuk intl, libzip-dev untuk zip, libjpeg-dev/libpng-dev/libfreetype6-dev untuk gd
# Tambahkan nodejs dan npm untuk membangun aset frontend (Vite/Mix)
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

# Konfigurasi dan instal ekstensi PHP
RUN docker-php-ext-configure intl
RUN docker-php-ext-configure gd '--with-jpeg' '--with-freetype'
RUN docker-php-ext-install -j$(nproc) intl opcache pdo_mysql zip gd # -j$(nproc) untuk build paralel
RUN pecl install xdebug && docker-php-ext-enable xdebug # Instal dan aktifkan Xdebug
RUN a2enmod rewrite # Aktifkan mod_rewrite untuk URL cantik Laravel

# --- Instalasi dan Konfigurasi APCU ---
RUN pecl install apcu-5.1.24 && docker-php-ext-enable apcu
# Tambahkan konfigurasi APCU ke php.ini (menggunakan >> untuk append)
RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/php.ini
RUN echo "apc.enable=1" >> /usr/local/etc/php/php.ini

# --- Instalasi Composer ---
# Unduh dan instal Composer secara global
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# --- Penyiapan Kode Aplikasi dan Dependensi ---

# Setel direktori kerja di dalam container ke root aplikasi Laravel
WORKDIR /var/www/html

# Salin file composer.json dan composer.lock terlebih dahulu untuk memanfaatkan caching Docker
# Jika hanya kode sumber yang berubah, langkah ini tidak akan diulang
COPY composer.json composer.lock ./

# Jalankan Composer install untuk mengunduh dependensi PHP
# --no-dev: tidak menginstal dependensi development
# --no-interaction: tidak meminta input
# --prefer-dist: mengunduh dari rilis stabil
# --optimize-autoloader: mengoptimalkan autoloader Composer
# --no-scripts: tidak menjalankan script composer (penting untuk CI/CD)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Salin file package.json dan package-lock.json untuk dependensi Node.js
COPY package.json package-lock.json ./

# Instal dependensi Node.js dan bangun aset frontend (menggunakan Vite/Mix)
RUN npm install
RUN npm run build # Asumsi Anda memiliki script 'build' di package.json
# Jika 'npm run build' mungkin gagal karena alasan tertentu (misalnya, hanya warning),
# Anda bisa menambahkan '|| true' di akhir baris: RUN npm run build || true

# Salin sisa kode aplikasi dari host ke container
# Ini harus dilakukan setelah instalasi dependensi untuk caching yang optimal
COPY . .

# Berikan izin yang benar untuk direktori storage dan bootstrap/cache
# Penting untuk operasi Laravel
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# --- Konfigurasi Akhir ---

# Ekspos port 80 agar dapat diakses dari luar container
EXPOSE 80

# Perintah default untuk menjalankan Apache di foreground
# Ini penting agar container tetap berjalan
CMD ["apache2-foreground"]
