# SNADNEE Laravel assignment

Project is built as a monorepo containing two applications.

## Back end
`snadnee-assignment-api` contains API implemented in Laravel 11.
API is RESTful. Authentication is implemented using Sanctum with 
bearer tokens.

Database schema is following.

![title](erd.png)

## Front end
`snadnee-assignment-fe` contains front end single page application implemented in Vue.js 3.
Front end app contains login and registration abilities. Logged users can search for 
free tables, create new reservations or cancel existing.

## Running
Whole infrastructure can be started by simple `docker compose up` command. Docker build handles
server setup, database migration, database seeding and front end building.

You can access adminer at http://localhost:8081\
Front end: http://localhost:8080\
Back end: http://localhost:8083

## Testing
You can run tests in container using `docker exec -it snadnee-laravel-assignment-php_api-1 ./vendor/bin/phpunit`
or in local system with installed PHP using `./vendor/bin/phpunit`