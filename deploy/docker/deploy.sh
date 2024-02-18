#!/bin/bash
NAME=$1
NON_SSL_PORT=$2
ID=$(id -u $USER)

docker build -t $NAME .
cd ../../

# create the container
docker run -d --name $NAME \
    -v "$PWD"/deploy/nginx/conf.d:/etc/nginx/conf.d \
    -v "$PWD"/:/var/www/app \
    -p $NON_SSL_PORT:80 \
    $NAME

docker exec -it $NAME mkdir -p /home/$NAME
docker exec -it $NAME useradd -r -u "$ID" -g sudo $NAME
docker exec -it $NAME chown -R $NAME:sudo /home/$NAME/

# initially this should be run, so the project actually works,
# these can be run anytime if needed afterwards
docker exec -it --user $NAME $NAME composer install
docker exec -it --user $NAME $NAME composer update
