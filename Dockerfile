# https://hub.docker.com/_/php
FROM php:7.2-fpm
COPY . /var/www/property_system
WORKDIR /var/www/property_system

# Install extensions
RUN apt-get update \
&& apt-get -y --no-install-recommends install  php7.2-mysql php-xdebug php7.2-bcmath php7.2-bz2 php7.2-gd php7.2-intl php-ssh2 php7.2-xsl php-yaml zip unzip php-zip \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install phalcon
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash \
&& apt-get install -y php7.2-phalcon=3.4.2-2+php7.2 \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
&& apt-get -y install git \
&& apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Phalcon
WORKDIR /usr/local/src
RUN git clone https://github.com/phalcon/cphalcon.git --branch v3.3.1 --single-branch
WORKDIR /usr/local/src/cphalcon/build
RUN ./install

WORKDIR /etc/php7/mods-available
RUN echo 'extension=phalcon.so' >> phalcon.ini
RUN docker-php-ext-enable phalcon


# Phalcon dev tools
RUN rm -rf ~/phalcon-devtools
RUN cd ~  && git clone git://github.com/phalcon/phalcon-devtools.git \
&& cd phalcon-devtools/ && ./phalcon.sh \
&& ln -s ~/phalcon-devtools/phalcon.php /usr/bin/phalcon  \
#&& chmod ugo+x /usr/bin/phalcon


# Install composer
WORKDIR /tmp
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Execute composer
WORKDIR /var/www/property_system/src
RUN composer install --ignore-platform-reqs --no-interaction

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
