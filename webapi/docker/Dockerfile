FROM php:8-fpm-alpine

RUN apk add postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql

COPY . /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

RUN composer dump-env prod

RUN bin/console cache:warmup

CMD ["php-fpm"]