migrate:
	@php sms migrations:migrate

test:
	vendor/bin/phpunit

clear-cache:
	@php sms  orm:clear-cache:result 

# only for development
up:
	cd docker && docker-compose up -d

up-build:
	cd docker && docker-compose up -d --build

down:
	cd docker && docker-compose down
