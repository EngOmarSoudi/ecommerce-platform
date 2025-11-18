# Mobile Authentication Flows

This document describes the authentication flows available in the e-commerce platform API for mobile applications.

## 1. Email/Password Authentication

### Registration

**Endpoint:** `POST /api/v1/auth/register`

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1234567890"
}
```

**Response:**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  "token_type": "Bearer"
}
```

### Login

**Endpoint:** `POST /api/v1/auth/login`

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  "token_type": "Bearer"
}
```

### Logout

**Endpoint:** `POST /api/v1/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Logged out successfully"
}
```

## 2. Phone + OTP Authentication

### Request OTP

**Endpoint:** `POST /api/v1/auth/phone/request-otp`

**Request:**
```json
{
  "phone": "+1234567890"
}
```

**Response:**
```json
{
  "message": "OTP sent successfully",
  "phone": "+1234567890"
}
```

### Verify OTP

**Endpoint:** `POST /api/v1/auth/phone/verify-otp`

**Request:**
```json
{
  "phone": "+1234567890",
  "otp": "123456",
  "name": "John Doe" // Optional, only required for new users
}
```

**Response:**
```json
{
  "message": "Authentication successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "+1234567890@phone.local",
    "phone": "+1234567890",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  "token_type": "Bearer"
}
```

## 3. Social Authentication

### Social Login

**Endpoint:** `POST /api/v1/auth/social/login`

**Request:**
```json
{
  "provider": "google",
  "provider_id": "123456789",
  "name": "John Doe",
  "email": "john@example.com",
  "avatar": "https://example.com/avatar.jpg"
}
```

**Response:**
```json
{
  "message": "Social login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "provider": "google",
    "provider_id": "123456789",
    "avatar": "https://example.com/avatar.jpg",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  },
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  "token_type": "Bearer"
}
```

## 4. User Profile Management

### Get Current User

**Endpoint:** `GET /api/v1/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "provider": null,
    "provider_id": null,
    "avatar": null,
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T10:00:00.000000Z"
  }
}
```

### Update User Profile

**Endpoint:** `PUT /api/v1/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
  "name": "John Smith",
  "email": "johnsmith@example.com",
  "phone": "+1234567890"
}
```

**Response:**
```json
{
  "message": "Profile updated successfully",
  "user": {
    "id": 1,
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "phone": "+1234567890",
    "created_at": "2025-11-18T10:00:00.000000Z",
    "updated_at": "2025-11-18T11:00:00.000000Z"
  }
}
```

## 5. Error Responses

All authentication endpoints return appropriate HTTP status codes and error messages:

- **401 Unauthorized**: Invalid credentials or authentication required
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server-side errors

Example error response:
```json
{
  "error": "Validation failed",
  "messages": {
    "email": [
      "The email field is required."
    ]
  }
}
```