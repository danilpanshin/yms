FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk update && apk add \
    build-base \
    libzip-dev \
    zip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    curl \
    bash \
    mysql-client \
    jpegoptim optipng pngquant gifsicle libavif

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd exif
RUN docker-php-ext-enable exif

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 9000
CMD ["php-fpm"]