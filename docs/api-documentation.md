# API Documentation

## Swagger/OpenAPI Documentation

The API documentation is automatically generated using L5-Swagger and is available in both JSON and UI formats.

### Accessing the Documentation

1. **Swagger UI**: Visit `/api/documentation` on your application to view the interactive API documentation.
2. **JSON Format**: The raw OpenAPI specification is available at `storage/api-docs/api-docs.json`.

### Regenerating Documentation

To regenerate the API documentation after making changes to the code:

```bash
php artisan l5-swagger:generate
```

## Postman Collection

A Postman collection is also available for easier API testing and development.

### Importing the Collection

1. Open Postman
2. Click on "Import" in the top left corner
3. Select the file `storage/api-docs/ecommerce-api-postman-collection.json`
4. The collection will be imported with all API endpoints pre-configured

### Using the Collection

The collection includes:
- All authentication endpoints (register, login, logout, phone auth, social login)
- User management endpoints (get current user, update user)
- Public endpoints (categories, products)

The collection uses variables for the base URL and authentication token:
- `{{base_url}}` - Default: http://localhost:8000
- `{{auth_token}}` - Set this after logging in to test protected endpoints

## API Endpoints Overview

### Authentication
- `POST /api/v1/auth/register` - Register a new user
- `POST /api/v1/auth/login` - Login with email and password
- `POST /api/v1/auth/logout` - Logout (requires authentication)
- `POST /api/v1/auth/phone/request-otp` - Request OTP for phone authentication
- `POST /api/v1/auth/phone/verify-otp` - Verify OTP and login/register
- `POST /api/v1/auth/social/login` - Social login

### User Management
- `GET /api/v1/me` - Get current user information (requires authentication)
- `PUT /api/v1/me` - Update current user information (requires authentication)

### Public Endpoints
- `GET /api/v1/categories` - Get all categories
- `GET /api/v1/products` - Get all products (paginated)

## Authentication

The API uses Laravel Sanctum for authentication. After logging in or registering, you'll receive a token that should be included in the `Authorization` header for protected endpoints:

```
Authorization: Bearer YOUR_TOKEN_HERE
```