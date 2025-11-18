#!/bin/bash

# Security Audit Script for E-Commerce Platform
# Runs comprehensive security checks before production deployment

set -e

echo "=========================================="
echo "Security Audit - E-Commerce Platform"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

ISSUES_FOUND=0

print_header() {
    echo -e "${BLUE}$1${NC}"
    echo "-----------------------------------"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
    ((ISSUES_FOUND++))
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
    ((ISSUES_FOUND++))
}

# 1. Dependency Audit
print_header "1. Dependency Security Audit"
echo "Checking PHP dependencies..."
if composer audit --format=json > /dev/null 2>&1; then
    print_success "No known vulnerabilities in PHP dependencies"
else
    print_error "Security vulnerabilities found in PHP dependencies!"
    echo "Run: composer audit"
fi

echo ""
echo "Checking NPM dependencies..."
if npm audit --audit-level=high --json > /dev/null 2>&1; then
    print_success "No critical vulnerabilities in NPM dependencies"
else
    print_warning "Vulnerabilities found in NPM dependencies"
    echo "Run: npm audit fix"
fi

# 2. Environment Configuration
print_header "2. Environment Configuration"
if [ ! -f .env ]; then
    print_error ".env file not found!"
else
    print_success ".env file exists"
    
    # Check APP_DEBUG
    if grep -q "APP_DEBUG=true" .env; then
        print_error "APP_DEBUG is set to true! Must be false in production"
    else
        print_success "APP_DEBUG is correctly set to false"
    fi
    
    # Check APP_KEY
    if ! grep -q "APP_KEY=base64:" .env; then
        print_error "APP_KEY is not set! Run: php artisan key:generate"
    else
        print_success "APP_KEY is set"
    fi
    
    # Check for default passwords
    if grep -q "password\|secret\|123456" .env; then
        print_warning "Potential weak passwords detected in .env"
    else
        print_success "No obvious weak passwords in .env"
    fi
fi

# 3. File Permissions
print_header "3. File Permissions Check"
if [ -d storage ] && [ -w storage ]; then
    print_success "storage/ directory is writable"
else
    print_error "storage/ directory is not writable!"
fi

if [ -d bootstrap/cache ] && [ -w bootstrap/cache ]; then
    print_success "bootstrap/cache/ directory is writable"
else
    print_error "bootstrap/cache/ directory is not writable!"
fi

# Check for overly permissive permissions
if find . -type f -perm 0777 | grep -q .; then
    print_warning "Files with 777 permissions found (security risk)"
else
    print_success "No files with 777 permissions"
fi

# 4. Security Headers
print_header "4. Security Headers Check"
if grep -q "SecurityHeadersMiddleware" app/Http/Kernel.php 2>/dev/null; then
    print_success "SecurityHeadersMiddleware is configured"
else
    print_warning "SecurityHeadersMiddleware not found in Kernel.php"
fi

# 5. HTTPS Configuration
print_header "5. HTTPS Configuration"
if grep -q "FORCE_HTTPS=true" .env; then
    print_success "HTTPS enforcement is enabled"
else
    print_warning "HTTPS enforcement not configured"
fi

# 6. Database Security
print_header "6. Database Security"
if grep -q "DB_PASSWORD=" .env && ! grep -q "DB_PASSWORD=$" .env; then
    print_success "Database password is set"
else
    print_error "Database password is not set!"
fi

# 7. CSRF Protection
print_header "7. CSRF Protection"
if grep -q "VerifyCsrfToken" app/Http/Kernel.php; then
    print_success "CSRF protection middleware is active"
else
    print_error "CSRF protection middleware not found!"
fi

# 8. Rate Limiting
print_header "8. Rate Limiting"
if [ -f app/Http/Middleware/RateLimitMiddleware.php ]; then
    print_success "RateLimitMiddleware exists"
else
    print_warning "RateLimitMiddleware not found"
fi

# 9. Sensitive Files
print_header "9. Sensitive Files Check"
SENSITIVE_FILES=(".env" "composer.lock" "package-lock.json")
for file in "${SENSITIVE_FILES[@]}"; do
    if grep -q "^$file$" .gitignore; then
        print_success "$file is in .gitignore"
    else
        print_warning "$file should be in .gitignore"
    fi
done

# 10. Code Quality
print_header "10. Code Quality Checks"
if [ -f vendor/bin/phpstan ]; then
    echo "Running PHPStan analysis..."
    if vendor/bin/phpstan analyse --memory-limit=1G --error-format=raw --no-progress > /dev/null 2>&1; then
        print_success "PHPStan analysis passed"
    else
        print_warning "PHPStan found potential issues"
    fi
fi

# 11. Git Security
print_header "11. Git Security"
if git log --all --pretty=format: --name-only | grep -q "\.env$"; then
    print_error ".env file was committed to git history!"
else
    print_success "No .env file in git history"
fi

# 12. Docker Security
print_header "12. Docker Security (if applicable)"
if [ -f Dockerfile ]; then
    if grep -q "USER www-data" Dockerfile; then
        print_success "Docker container runs as non-root user"
    else
        print_warning "Docker container may run as root"
    fi
fi

# Summary
echo ""
echo "=========================================="
echo "Security Audit Summary"
echo "=========================================="
echo ""

if [ $ISSUES_FOUND -eq 0 ]; then
    echo -e "${GREEN}✓ All security checks passed!${NC}"
    echo ""
    echo "Next steps:"
    echo "  1. Review SECURITY_CHECKLIST.md"
    echo "  2. Schedule penetration testing"
    echo "  3. Configure monitoring and alerts"
    echo ""
    exit 0
else
    echo -e "${RED}✗ Found $ISSUES_FOUND security issue(s)${NC}"
    echo ""
    echo "Please address the issues above before deploying to production."
    echo "Review SECURITY_CHECKLIST.md for detailed mitigation steps."
    echo ""
    exit 1
fi
