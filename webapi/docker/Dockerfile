FROM php:8-fpm-alpine

RUN apk add postgresql-dev autoconf gcc g++ make libffi-dev openssl-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN pecl install -o -f redis &&  rm -rf /tmp/pear &&  docker-php-ext-enable redis

COPY . /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

RUN composer dump-env prod

RUN bin/console cache:warmup

CMD ["php-fpm"]