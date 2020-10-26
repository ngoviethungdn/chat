# Chat system
Simple chat with Laravel and Socket.io

## Getting Started

Build & run docker
```sh
$ docker-compose up -d
```

Run composer install
```sh
$ docker-compose run --rm composer install
```

Copy file .env
```sh
$ cp src/.env.example src/.env
```

Run artisans
```sh
$ docker-compose run --rm artisan key:generate
$ docker-compose run --rm artisan migrate --seed
```

Run node_modules install
```sh
$ docker-compose run --rm npm install
```

Verify on browser.
```sh
http://localhost
```