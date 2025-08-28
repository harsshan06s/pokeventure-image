FROM php:7-fpm

RUN apt-get update -y && apt-get install -y libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6-dev
RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev libonig-dev
RUN apt-get install -y nginx
RUN apt-get install -y build-essential
RUN apt-get install -y git

RUN git clone https://github.com/ImageMagick/ImageMagick.git ImageMagick-7.1.0

RUN cd ImageMagick-7.1.0 && ./configure && make && make install && ldconfig /usr/local/lib

RUN docker-php-ext-install mbstring

RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd

RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/php-opocache-cfg.ini

COPY site.conf /etc/nginx/sites-enabled/default
RUN echo 'display_errors = "Off"' >> /usr/local/etc/php/conf.d/disable-warnings.ini
COPY entrypoint.sh /etc/entrypoint.sh
RUN ["chmod", "+x", "/etc/entrypoint.sh"]

COPY --chown=www-data:www-data src/ /var/www/myapp

WORKDIR /var/www/myapp

EXPOSE 80 443

ENTRYPOINT ["/etc/entrypoint.sh"]