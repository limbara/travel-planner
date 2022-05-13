# travel-planner
simple travel planner api. built with Laravel 9.X. 

# missing :
* **Type‌ ‌of‌ ‌the‌ ‌trip** can't really think what this values should be

## Instalation Requirement
* [Docker](https://docs.docker.com/get-docker) 

## To Run Command
if you haven't build the images or anything related to image dockerfile changes:
* `docker-compose up -d --build`

if you have build before:
* `docker-compose up -d`

## To Stop Command
if you want to stop:
* `docker-compose down`

## To Run Laravel Commands
* `docker-compose exec server sh` and then type your laravel commands
* `php artisan migrate`
* `php artisan db:seed`