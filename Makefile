#aliases

start: docker-down \
	docker-up \
	docker-composer-build \
	docker-create-stream \

stop: docker-down

cc: docker-cache-clear
ci: docker-composer-build

# commands

docker-down:
	docker-compose down

docker-up:
	docker-compose up -d --build

docker-composer-build:
	docker-compose exec php composer install -d /var/www/html -n

docker-cache-clear:
	docker-compose exec php rm -rf /var/www/html/var/cache/*
	docker-compose exec php chmod -R 777 /var/www/html/var/cache/

docker-create-stream:
	docker-compose exec php php bin/console event-store:event-stream:create