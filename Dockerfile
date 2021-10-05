# https://hub.docker.com/_/php
FROM php:7.2-fpm
COPY . /var/www/property_system
WORKDIR /var/www/property_system

# Install extensions
RUN apt-get update \
&& apt-get -y --no-install-recommends install zip unzip \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install phalcon
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
&& apt-get install -y php7.2-phalcon=3.4.2-2+php7.2 \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
&& apt-get -y install git \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install mysqli
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Install Phalcon
WORKDIR /usr/local/src
RUN git clone https://github.com/phalcon/cphalcon.git --branch v3.3.1 --single-branch
WORKDIR /usr/local/src/cphalcon/build
RUN ./install

WORKDIR /etc/php7/mods-available
RUN echo 'extension=phalcon.so' >> phalcon.ini
RUN docker-php-ext-enable phalcon

# install yaml
RUN apt-get -y update && apt-get -y install libyaml-dev && printf "\n" | pecl install yaml-2.0.0

#RUN apt-get install libyaml-dev && printf "\n" | pecl install yaml-2.0.0
RUN echo 'extension=yaml.so' >> yaml.ini
RUN docker-php-ext-enable yaml

# Install composer
WORKDIR /tmp
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Execute composer
WORKDIR /var/www/property_system/src
RUN composer install --ignore-platform-reqs --no-interaction

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
