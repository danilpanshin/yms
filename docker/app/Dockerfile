FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libzip-dev \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    default-mysql-client \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    libavif-dev \
    firebird-dev \
    libfbclient2 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_firebird \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

EXPOSE 9000
CMD ["php-fpm"]