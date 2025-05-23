.PHONY: start stop clean tests shell migrate

start:
	@docker-compose up -d --build

stop:
	@docker-compose stop

clean:
	@docker-compose down --volumes --remove-orphans

tests:
	@docker-compose run --rm app vendor/bin/phpunit --testdox

shell:
	@docker-compose exec app /bin/bash

migrate:
	@docker-compose exec app php artisan migrate
