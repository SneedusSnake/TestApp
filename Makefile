install: composer
	cp .env.example .env
	./vendor/bin/sail up -d
	./vendor/bin/sail php artisan key:generate
	./vendor/bin/sail npm install
	./vendor/bin/sail npm run build
	./vendor/bin/sail php artisan migrate

start:
	./vendor/bin/sail up -d

seed: start
	./vendor/bin/sail php artisan db:seed

composer:
	docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(CURDIR):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

