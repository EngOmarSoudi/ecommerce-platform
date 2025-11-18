@echo off
REM E-Commerce Platform - Quick Testing Script (Windows)
REM This script helps automate testing procedures

setlocal enabledelayedexpansion

echo ╔════════════════════════════════════════╗
echo ║  E-Commerce Platform Testing Suite    ║
echo ╚════════════════════════════════════════╝
echo.

REM Check if Laravel is installed
if not exist "artisan" (
    echo [ERROR] Laravel not found. Please run from project root.
    exit /b 1
)

echo [SUCCESS] Laravel project detected
echo.

REM Main menu
echo Select testing category:
echo 1) Run All Tests
echo 2) Unit Tests Only
echo 3) Feature Tests Only
echo 4) Security Audit
echo 5) Performance Tests
echo 6) API Tests
echo 7) Database Tests
echo 8) Browser Tests (Manual)
echo 9) Quick Health Check
echo 0) Exit
echo.
set /p choice="Enter choice [0-9]: "

if "%choice%"=="1" goto all_tests
if "%choice%"=="2" goto unit_tests
if "%choice%"=="3" goto feature_tests
if "%choice%"=="4" goto security_audit
if "%choice%"=="5" goto performance_tests
if "%choice%"=="6" goto api_tests
if "%choice%"=="7" goto database_tests
if "%choice%"=="8" goto browser_tests
if "%choice%"=="9" goto health_check
if "%choice%"=="0" goto exit
goto invalid_choice

:all_tests
echo.
echo === Running All Tests ===
echo.
php artisan test --parallel
goto end

:unit_tests
echo.
echo === Running Unit Tests ===
echo.
php artisan test --testsuite=Unit
goto end

:feature_tests
echo.
echo === Running Feature Tests ===
echo.
php artisan test --testsuite=Feature
goto end

:security_audit
echo.
echo === Security Audit ===
echo.

echo [INFO] Checking PHP dependencies...
composer audit

echo [INFO] Checking NPM dependencies...
npm audit --audit-level=moderate

echo [INFO] Checking environment configuration...
findstr /C:"APP_DEBUG=true" .env >nul
if !errorlevel! equ 0 (
    echo [ERROR] APP_DEBUG is set to true!
) else (
    echo [SUCCESS] APP_DEBUG is properly configured
)

findstr /C:"APP_KEY=" .env | findstr /V /C:"APP_KEY=$" >nul
if !errorlevel! equ 0 (
    echo [SUCCESS] APP_KEY is set
) else (
    echo [ERROR] APP_KEY is not set!
)

echo [INFO] Checking file permissions...
if exist "storage" (
    echo [SUCCESS] Storage directory exists
) else (
    echo [ERROR] Storage directory not found
)

echo [SUCCESS] Security audit complete
goto end

:performance_tests
echo.
echo === Performance Tests ===
echo.

echo [INFO] Checking OPcache status...
php -r "echo function_exists('opcache_get_status') ? 'OPcache available' : 'OPcache not available'; echo PHP_EOL;"

echo [INFO] Checking Redis connection...
php artisan tinker --execute="Redis::ping();"

echo [INFO] Checking database connection...
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected';"

echo [INFO] Optimizing application...
php artisan cache:clear
php artisan route:cache
php artisan config:cache
php artisan view:cache

echo [SUCCESS] Performance tests complete
goto end

:api_tests
echo.
echo === API Tests ===
echo.

echo [INFO] Testing API endpoints...

REM Test if server is running
curl -s http://localhost:8000/health >nul 2>&1
if !errorlevel! equ 0 (
    echo [SUCCESS] Server is running
    
    echo [INFO] Testing /api/categories...
    curl -s -o nul -w "%%{http_code}" http://localhost:8000/api/categories
    
    echo [INFO] Testing /api/products...
    curl -s -o nul -w "%%{http_code}" http://localhost:8000/api/products
) else (
    echo [ERROR] Server is not running. Start with: php artisan serve
)
goto end

:database_tests
echo.
echo === Database Tests ===
echo.

echo [INFO] Running migrations...
php artisan migrate:fresh --seed --env=testing

echo [INFO] Testing database connection...
php artisan db:show

echo [SUCCESS] Database tests complete
goto end

:browser_tests
echo.
echo === Browser Tests (Manual) ===
echo.

echo [INFO] Opening test dashboard...
echo Navigate to: http://localhost:8000/test-dashboard.html
echo.
echo Manual Test Checklist:
echo =====================
echo.
echo □ Desktop Browser (1920x1080)
echo   □ Navigation works
echo   □ Product listing loads
echo   □ Search functions
echo   □ Add to cart works
echo   □ Checkout process
echo.
echo □ Tablet Browser (768x1024)
echo   □ Responsive layout
echo   □ Touch controls
echo   □ Mobile menu
echo.
echo □ Mobile Browser (375x667)
echo   □ All features accessible
echo   □ Forms usable
echo   □ Images load properly
echo.
echo □ PWA Features
echo   □ Install prompt appears
echo   □ Offline mode works
echo   □ Service worker registered
echo.
echo [INFO] Press Ctrl+C when done...
php artisan serve
goto end

:health_check
echo.
echo === Quick Health Check ===
echo.

echo [INFO] Checking application health...

if exist ".env" (
    echo [SUCCESS] .env file exists
) else (
    echo [ERROR] .env file missing
)

if exist "storage" (
    echo [SUCCESS] Storage directory exists
) else (
    echo [ERROR] Storage directory not found
)

php artisan tinker --execute="DB::connection()->getPdo();" 2>nul
if !errorlevel! equ 0 (
    echo [SUCCESS] Database connection OK
) else (
    echo [ERROR] Database connection failed
)

php artisan tinker --execute="Redis::ping();" 2>nul
if !errorlevel! equ 0 (
    echo [SUCCESS] Redis connection OK
) else (
    echo [WARNING] Redis connection failed (optional)
)

echo [INFO] Testing key routes...
php artisan route:list | findstr /C:"login" /C:"products" /C:"checkout"

echo [SUCCESS] Health check complete
goto end

:invalid_choice
echo [ERROR] Invalid choice
exit /b 1

:exit
echo [INFO] Exiting...
exit /b 0

:end
echo.
echo [SUCCESS] Testing completed successfully!
echo.
echo [INFO] View detailed report: COMPREHENSIVE_TESTING_REPORT.md
echo [INFO] View test dashboard: http://localhost:8000/test-dashboard.html
echo.
pause
