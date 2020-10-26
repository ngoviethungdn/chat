FROM node:13.7

RUN npm install -g laravel-echo-server

WORKDIR /var/www/html

CMD laravel-echo-server start
