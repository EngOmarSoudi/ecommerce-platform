#!/bin/bash

# E-Commerce Platform - Quick Testing Script
# This script helps automate testing procedures

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║  E-Commerce Platform Testing Suite    ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════╝${NC}"
echo ""

# Function to print section header
print_section() {
    echo -e "\n${BLUE}═══ $1 ═══${NC}\n"
}

# Function to print success
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

# Function to print error
print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Function to print warning
print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

# Function to print info
print_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

# Check if Laravel is installed
if [ ! -f "artisan" ]; then
    print_error "Laravel not found. Please run from project root."
    exit 1
fi

print_success "Laravel project detected"
echo ""

# Main menu
echo "Select testing category:"
echo "1) Run All Tests"
echo "2) Unit Tests Only"
echo "3) Feature Tests Only"
echo "4) Security Audit"
echo "5) Performance Tests"
echo "6) API Tests"
echo "7) Database Tests"
echo "8) Browser Tests (Manual)"
echo "9) Quick Health Check"
echo "0) Exit"
echo ""
read -p "Enter choice [0-9]: " choice

case $choice in
    1)
        print_section "Running All Tests"
        php artisan test --parallel
        ;;
    2)
        print_section "Running Unit Tests"
        php artisan test --testsuite=Unit
        ;;
    3)
        print_section "Running Feature Tests"
        php artisan test --testsuite=Feature
        ;;
    4)
        print_section "Security Audit"
        
        print_info "Checking PHP dependencies..."
        composer audit
        
        print_info "Checking NPM dependencies..."
        npm audit --audit-level=moderate
        
        print_info "Checking environment configuration..."
        if grep -q "APP_DEBUG=true" .env; then
            print_error "APP_DEBUG is set to true!"
        else
            print_success "APP_DEBUG is properly configured"
        fi
        
        if grep -q "APP_KEY=" .env | grep -v "APP_KEY=$"; then
            print_success "APP_KEY is set"
        else
            print_error "APP_KEY is not set!"
        fi
        
        print_info "Checking file permissions..."
        if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
            print_success "File permissions are correct"
        else
            print_error "File permissions need adjustment"
        fi
        
        print_success "Security audit complete"
        ;;
    5)
        print_section "Performance Tests"
        
        print_info "Checking OPcache status..."
        php -r "echo function_exists('opcache_get_status') ? 'OPcache available' : 'OPcache not available'; echo PHP_EOL;"
        
        print_info "Checking Redis connection..."
        php artisan tinker --execute="Redis::ping();"
        
        print_info "Running database query test..."
        php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected';"
        
        print_info "Checking cache performance..."
        php artisan cache:clear
        time php artisan route:cache
        time php artisan config:cache
        time php artisan view:cache
        
        print_success "Performance tests complete"
        ;;
    6)
        print_section "API Tests"
        
        print_info "Testing API endpoints..."
        
        # Check if server is running
        if curl -s http://localhost:8000/health > /dev/null; then
            print_success "Server is running"
            
            print_info "Testing /api/categories..."
            curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/categories
            
            print_info "Testing /api/products..."
            curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/api/products
        else
            print_error "Server is not running. Start with: php artisan serve"
        fi
        ;;
    7)
        print_section "Database Tests"
        
        print_info "Running migrations..."
        php artisan migrate:fresh --seed --env=testing
        
        print_info "Testing database connection..."
        php artisan db:show
        
        print_info "Checking database indexes..."
        php artisan db:table products
        
        print_success "Database tests complete"
        ;;
    8)
        print_section "Browser Tests (Manual)"
        
        print_info "Opening test dashboard..."
        print_info "Navigate to: http://localhost:8000/test-dashboard.html"
        
        echo ""
        echo "Manual Test Checklist:"
        echo "====================="
        echo ""
        echo "□ Desktop Browser (1920x1080)"
        echo "  □ Navigation works"
        echo "  □ Product listing loads"
        echo "  □ Search functions"
        echo "  □ Add to cart works"
        echo "  □ Checkout process"
        echo ""
        echo "□ Tablet Browser (768x1024)"
        echo "  □ Responsive layout"
        echo "  □ Touch controls"
        echo "  □ Mobile menu"
        echo ""
        echo "□ Mobile Browser (375x667)"
        echo "  □ All features accessible"
        echo "  □ Forms usable"
        echo "  □ Images load properly"
        echo ""
        echo "□ PWA Features"
        echo "  □ Install prompt appears"
        echo "  □ Offline mode works"
        echo "  □ Service worker registered"
        echo ""
        
        print_info "Press Ctrl+C when done..."
        php artisan serve
        ;;
    9)
        print_section "Quick Health Check"
        
        print_info "Checking application health..."
        
        # Check .env file
        if [ -f ".env" ]; then
            print_success ".env file exists"
        else
            print_error ".env file missing"
        fi
        
        # Check storage permissions
        if [ -w "storage" ]; then
            print_success "Storage directory writable"
        else
            print_error "Storage directory not writable"
        fi
        
        # Check database connection
        if php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
            print_success "Database connection OK"
        else
            print_error "Database connection failed"
        fi
        
        # Check Redis connection
        if php artisan tinker --execute="Redis::ping();" 2>/dev/null; then
            print_success "Redis connection OK"
        else
            print_warning "Redis connection failed (optional)"
        fi
        
        # Check key routes
        print_info "Testing key routes..."
        php artisan route:list | grep -E "(login|products|checkout)" | head -n 5
        
        print_success "Health check complete"
        ;;
    0)
        print_info "Exiting..."
        exit 0
        ;;
    *)
        print_error "Invalid choice"
        exit 1
        ;;
esac

echo ""
print_success "Testing completed successfully!"
echo ""
print_info "View detailed report: COMPREHENSIVE_TESTING_REPORT.md"
print_info "View test dashboard: http://localhost:8000/test-dashboard.html"
echo ""
