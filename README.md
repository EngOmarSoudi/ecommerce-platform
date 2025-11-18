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
# or
make test
```

To run code style checks:
```bash
docker-compose exec app ./vendor/bin/php-cs-fixer fix
# or
make fix
```

To run static analysis:
```bash
docker-compose exec app ./vendor/bin/phpstan analyze
# or
make analyze
```

## Security

### Secret Handling Guidelines

1. **Never commit sensitive data** to the repository:
   - Use `.env` for all environment-specific configurations
   - Keep `.env` in `.gitignore`
   - Use `.env.example` as a template with dummy values

2. **Required secrets to configure**:
   - `APP_KEY`: Generate with `php artisan key:generate`
   - Database credentials: `DB_*` variables
   - Mail credentials: `MAIL_*` variables  
   - AWS credentials: `AWS_*` variables (if using S3)
   - OAuth keys: Configure for social login providers

3. **For production deployments**:
   - Use environment variables or secret management services (AWS Secrets Manager, HashiCorp Vault)
   - Rotate keys regularly
   - Use strong, unique passwords
   - Enable 2FA for all admin accounts

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).