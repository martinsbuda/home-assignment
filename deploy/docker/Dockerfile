FROM ubuntu:22.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt update -y
RUN apt upgrade -y
# Essentials
RUN apt install software-properties-common wget curl git vim wget -y

# Supervisor
RUN apt install supervisor -y
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP and packages
RUN apt install language-pack-en-base -y
RUN apt install software-properties-common -y
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php -y
RUN apt update -y
RUN apt install php8.2-cli php8.2-fpm php8.2 php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-pgsql php8.2-curl nginx -y

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN service php8.2-fpm stop
RUN mkdir -p /var/run/php

# PHP Config
RUN sed -i '/^short_open_tag =/cshort_open_tag = On' /etc/php/8.2/cli/php.ini && \
sed -i '/^short_open_tag =/cshort_open_tag = On' /etc/php/8.2/fpm/php.ini && \
sed -i '/^error_reporting =/cerror_reporting = E_ALL & ~E_NOTICE & ~E_WARNING' /etc/php/8.2/fpm/php.ini && \
sed -i '/^error_reporting =/cerror_reporting = E_ALL & ~E_NOTICE & ~E_WARNING' /etc/php/8.2/cli/php.ini && \
sed -i '/^;daemonize =/cdaemonize = no' /etc/php/8.2/fpm/php-fpm.conf && \
sed -i '/^expose_php = On/cexpose_php = Off' /etc/php/8.2/fpm/php.ini && \
sed -i '/^upload_max_filesize =/cupload_max_filesize = 50M' /etc/php/8.2/cli/php.ini && \
sed -i '/^upload_max_filesize =/cupload_max_filesize = 50M' /etc/php/8.2/fpm/php.ini && \
sed -i '/^max_execution_time =/cmax_execution_time = 300' /etc/php/8.2/cli/php.ini && \
sed -i '/^max_execution_time =/cmax_execution_time = 300' /etc/php/8.2/fpm/php.ini

# NGINX
RUN apt update && apt install nginx -y
COPY nginx.conf /etc/nginx/nginx.conf
RUN rm /etc/nginx/sites-enabled/default

COPY default.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www/app
CMD ["/usr/bin/supervisord"]
