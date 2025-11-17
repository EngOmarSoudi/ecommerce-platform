# Makefile for E-commerce Platform

# Install dependencies
install:
	composer install
	npm install

# Build assets
build:
	npm run build

# Generate application key
key:
	php artisan key:generate

# Run migrations
migrate:
	php artisan migrate

# Run tests
test:
	php artisan test

# Run tests with coverage
test-coverage:
	php artisan test --coverage

# Start development server
serve:
	php artisan serve

# Run PHP-CS-Fixer
fix:
	./vendor/bin/php-cs-fixer fix

# Run PHP-CS-Fixer in dry-run mode
check:
	./vendor/bin/php-cs-fixer fix --dry-run --diff

# Start Docker containers
docker-up:
	docker-compose up -d

# Stop Docker containers
docker-down:
	docker-compose down

# Run tests in Docker
docker-test:
	docker-compose exec app php artisan test

# Install dependencies in Docker
docker-install:
	docker-compose exec app composer install

# Run migrations in Docker
docker-migrate:
	docker-compose exec app php artisan migrate

.PHONY: install build key migrate test test-coverage serve fix check docker-up docker-down docker-test docker-install docker-migrate