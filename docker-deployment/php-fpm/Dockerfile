FROM php:7.4-fpm

RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN ln -s /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN sed -i -e 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf
#RUN sed -i -e 's/;extension=pgsql/extension=pgsql/' /usr/local/etc/php/php.ini
#RUN sed -i -e 's/;extension=pdo_pgsql/extension=pdo_pgsql/' /usr/local/etc/php/php.ini

# Install PHP Extensions
RUN docker-php-ext-install mysqli

# Install PHP Extensions by pecl
 RUN pecl install -o -f redis \
 &&  rm -rf /tmp/pear \
 &&  docker-php-ext-enable redis

RUN groupadd -g 998 vboxsf
RUN usermod -a -G vboxsf root
RUN usermod -a -G vboxsf www-data

RUN mkdir /src && chown root:root /src -R && chmod 777 /src -R && chmod +r /src -R
#COPY ./src /src

VOLUME /src

RUN php-fpm