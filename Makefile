#aliases

start: docker-down \
	docker-up \
	docker-composer-build \

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

docker-setup-amqp:
	docker-compose exec command /var/command/amqp-tools/vendor/bin/rabbit vhost:mapping:create /var/command/amqp-tools/config/products.yml --host=rabbitmq --password=guest
