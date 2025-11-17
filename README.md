# E-commerce Platform

A comprehensive e-commerce platform built with Laravel, Filament, and TailwindCSS.

## Features

- Product catalog with categories and search
- Shopping cart and checkout system
- User authentication and authorization
- Admin panel with Filament
- RESTful API
- Docker support for easy deployment

## Requirements

- Docker
- Docker Compose

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   ```

2. Navigate to the project directory:
   ```bash
   cd ecommerce-platform
   ```

3. Start the Docker containers:
   ```bash
   docker-compose up -d
   ```

4. Install PHP dependencies:
   ```bash
   docker-compose exec app composer install
   ```

5. Generate application key:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. Run database migrations:
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. Access the application at http://localhost:8000

## Health Check Endpoint

The application provides a health check endpoint for container orchestration:

```
GET /api/health
```

## Development

To run tests:
```bash
docker-compose exec app php artisan test
```

To run code style checks:
```bash
docker-compose exec app ./vendor/bin/php-cs-fixer fix
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).