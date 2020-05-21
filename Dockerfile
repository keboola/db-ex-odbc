FROM php:7.2-cli-stretch

ARG DEBIAN_FRONTEND=noninteractive
ARG COMPOSER_FLAGS="--prefer-dist --no-interaction"
ARG RTK_LICENSE_BUILD_ARG
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_PROCESS_TIMEOUT 3600
ENV RTK_LICENSE=$RTK_LICENSE_BUILD_ARG

RUN apt-get update -q \
  && apt-get install -y --no-install-recommends \
    unzip \
    git \
    ssh \
    unixodbc \
    unixodbc-dev \
    libc6 \
    libstdc++6 \
    zlib1g \
    libgcc1 \
    expect \
  && rm -rf /var/lib/apt/lists/*

RUN set -x \
    && docker-php-source extract \
    && cd /usr/src/php/ext/odbc \
    && phpize \
    && sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure \
    && ./configure --with-unixODBC=shared,/usr \
    && docker-php-ext-install odbc \
    && docker-php-source delete

COPY docker/NetSuiteODBCDriver.deb /tmp/odbc.deb
#COPY docker/odbcinst.ini /etc/odbcinst.ini
RUN dpkg -i /tmp/odbc.deb
COPY docker/license /opt/cdata/cdata-odbc-driver-for-netsuite/bin/license
WORKDIR /opt/cdata/cdata-odbc-driver-for-netsuite/bin/
RUN ./license
RUN odbcinst -q -d

RUN echo "memory_limit = -1" >> /usr/local/etc/php/php.ini

WORKDIR /root

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer && composer global require hirak/prestissimo

WORKDIR /code

## Composer - deps always cached unless changed
# First copy only composer files
COPY composer.* /code/
# Download dependencies, but don't run scripts or init autoloaders as the app is missing
RUN composer install $COMPOSER_FLAGS --no-scripts --no-autoloader
# copy rest of the app
COPY . /code/
# run normal composer - all deps are cached already
RUN composer install $COMPOSER_FLAGS

CMD php ./src/run.php --data=/data
