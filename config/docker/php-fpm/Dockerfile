FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
        librabbitmq-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libssl-dev \
        git \
        zlib1g-dev \
        libicu-dev \
        g++ \
        supervisor \
    && docker-php-ext-install -j$(nproc) iconv zip intl bcmath pcntl pdo pdo_mysql  \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

RUN pecl install amqp \
    && docker-php-ext-enable amqp

ADD symfony.ini /etc/php/
ADD symfony.pool.conf /etc/php/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Create dirs
RUN mkdir -p /var/www/html/var/{cache,log}
RUN chmod -R 777 /var/www/html/var/{cache,log}

# Setup supervisor
COPY supervisord.conf /etc/supervisord.conf
COPY workers.ini /etc/supervisor.d/workers.ini