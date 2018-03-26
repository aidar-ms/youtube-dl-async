FROM php:7.2-apache

ENV WEB_DOCUMENT_ROOT  /app/web
ENV WEB_DOCUMENT_INDEX index.php


COPY vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /app

ADD . /app

# -- confs
RUN apt-get update && \
    apt-get install -y --allow-unauthenticated vim ffmpeg youtube-dl libgearman-dev supervisor gearman-tools gearman wget unzip && \
    cd /tmp && \
    wget https://github.com/wcgallego/pecl-gearman/archive/gearman-2.0.3.zip && \
    unzip gearman-2.0.3.zip && \
    mv pecl-gearman-gearman-2.0.3 pecl-gearman && \
    cd pecl-gearman && \
    phpize && \
    ./configure && \
    make -j$(nproc) && \
    make install && \
    cd / && \
    rm /tmp/gearman-2.0.3.zip && \
    rm -r /tmp/pecl-gearman && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-enable gearman && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/web && \
    a2enmod rewrite

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data

EXPOSE 80

CMD ["supervisord"]