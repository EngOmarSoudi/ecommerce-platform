#!/bin/bash

# Production Setup Script for E-Commerce Platform
# This script guides you through the production deployment setup

set -e

echo "=========================================="
echo "E-Commerce Platform - Production Setup"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if .env exists
echo "Step 1: Environment Configuration"
echo "-----------------------------------"
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    print_success ".env file created"
else
    print_warning ".env file already exists, skipping..."
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating APP_KEY..."
    php artisan key:generate
    print_success "APP_KEY generated"
else
    print_success "APP_KEY already set"
fi

echo ""
echo "Step 2: Security Audit"
echo "-----------------------------------"
echo "Running Composer security audit..."
if composer audit; then
    print_success "No security vulnerabilities found in PHP dependencies"
else
    print_error "Security vulnerabilities detected! Please review and update dependencies."
fi

echo ""
echo "Running NPM security audit..."
if npm audit --audit-level=moderate; then
    print_success "No critical vulnerabilities found in NPM dependencies"
else
    print_warning "NPM vulnerabilities detected. Run 'npm audit fix' to resolve."
fi

echo ""
echo "Step 3: Database Setup"
echo "-----------------------------------"
read -p "Have you configured database credentials in .env? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Testing database connection..."
    if php artisan migrate:status > /dev/null 2>&1; then
        print_success "Database connection successful"
    else
        print_error "Database connection failed. Please check your credentials."
        exit 1
    fi
fi

echo ""
echo "Step 4: Cache Configuration"
echo "-----------------------------------"
read -p "Have you configured Redis in .env? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Testing Redis connection..."
    if php artisan tinker --execute="Redis::ping();" > /dev/null 2>&1; then
        print_success "Redis connection successful"
    else
        print_error "Redis connection failed. Please check your configuration."
    fi
fi

echo ""
echo "Step 5: File Permissions"
echo "-----------------------------------"
echo "Setting correct file permissions..."
chmod -R 755 storage bootstrap/cache
print_success "File permissions set"

echo ""
echo "Step 6: Optimization"
echo "-----------------------------------"
echo "Clearing and caching configurations..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Application optimized for production"

echo ""
echo "=========================================="
echo "Production Setup Summary"
echo "=========================================="
echo ""
print_success "Environment configured"
print_success "Security audit completed"
print_success "Application optimized"
echo ""
print_warning "Next Steps:"
echo "  1. Configure GitHub Secrets (see DEPLOYMENT.md)"
echo "  2. Generate PWA icons at /pwa/generator.html"
echo "  3. Review SECURITY_CHECKLIST.md"
echo "  4. Schedule penetration testing"
echo ""
echo "Ready for deployment! 🚀"
