.PHONY: build up stop clean tests shell migrate

build:
	@docker-compose up -d --build

up:
	@docker-compose up -d

stop:
	@docker-compose stop

clean:
	@docker-compose down --volumes --remove-orphans

test:
	@docker-compose run --rm app vendor/bin/phpunit --testdox

shell:
	@docker-compose exec app /bin/bash

migrate:
	@docker-compose exec app php artisan migrate

unit:
	@docker-compose run --rm app vendor/bin/phpunit \
		--testsuite Unit --testdox

integration:
	@docker-compose run --rm app vendor/bin/phpunit \
		--testsuite Feature --testdox

tests: unit integration

