@echo off
REM Security Audit Script for E-Commerce Platform (Windows)
REM Runs comprehensive security checks before production deployment

echo ==========================================
echo Security Audit - E-Commerce Platform
echo ==========================================
echo.

set ISSUES_FOUND=0

echo 1. Dependency Security Audit
echo -----------------------------------
echo Checking PHP dependencies...
composer audit >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [32m[OK] No known vulnerabilities in PHP dependencies[0m
) else (
    echo [31m[ERROR] Security vulnerabilities found in PHP dependencies![0m
    echo Run: composer audit
    set /a ISSUES_FOUND+=1
)

echo.
echo Checking NPM dependencies...
npm audit --audit-level=high >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [32m[OK] No critical vulnerabilities in NPM dependencies[0m
) else (
    echo [33m[WARNING] Vulnerabilities found in NPM dependencies[0m
    echo Run: npm audit fix
    set /a ISSUES_FOUND+=1
)

echo.
echo 2. Environment Configuration
echo -----------------------------------
if not exist .env (
    echo [31m[ERROR] .env file not found![0m
    set /a ISSUES_FOUND+=1
) else (
    echo [32m[OK] .env file exists[0m
    
    findstr /C:"APP_DEBUG=true" .env >nul 2>&1
    if %ERRORLEVEL% EQU 0 (
        echo [31m[ERROR] APP_DEBUG is set to true! Must be false in production[0m
        set /a ISSUES_FOUND+=1
    ) else (
        echo [32m[OK] APP_DEBUG is correctly set to false[0m
    )
    
    findstr /C:"APP_KEY=base64:" .env >nul 2>&1
    if %ERRORLEVEL% NEQ 0 (
        echo [31m[ERROR] APP_KEY is not set! Run: php artisan key:generate[0m
        set /a ISSUES_FOUND+=1
    ) else (
        echo [32m[OK] APP_KEY is set[0m
    )
)

echo.
echo 3. File Permissions Check
echo -----------------------------------
if exist storage (
    echo [32m[OK] storage/ directory exists[0m
) else (
    echo [31m[ERROR] storage/ directory not found![0m
    set /a ISSUES_FOUND+=1
)

if exist bootstrap\cache (
    echo [32m[OK] bootstrap/cache/ directory exists[0m
) else (
    echo [31m[ERROR] bootstrap/cache/ directory not found![0m
    set /a ISSUES_FOUND+=1
)

echo.
echo 4. Security Headers Check
echo -----------------------------------
findstr /C:"SecurityHeadersMiddleware" app\Http\Kernel.php >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [32m[OK] SecurityHeadersMiddleware is configured[0m
) else (
    echo [33m[WARNING] SecurityHeadersMiddleware not found in Kernel.php[0m
    set /a ISSUES_FOUND+=1
)

echo.
echo 5. Rate Limiting
echo -----------------------------------
if exist app\Http\Middleware\RateLimitMiddleware.php (
    echo [32m[OK] RateLimitMiddleware exists[0m
) else (
    echo [33m[WARNING] RateLimitMiddleware not found[0m
    set /a ISSUES_FOUND+=1
)

echo.
echo 6. Sensitive Files
echo -----------------------------------
findstr /C:".env" .gitignore >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [32m[OK] .env is in .gitignore[0m
) else (
    echo [33m[WARNING] .env should be in .gitignore[0m
    set /a ISSUES_FOUND+=1
)

echo.
echo ==========================================
echo Security Audit Summary
echo ==========================================
echo.

if %ISSUES_FOUND% EQU 0 (
    echo [32m[OK] All security checks passed![0m
    echo.
    echo Next steps:
    echo   1. Review SECURITY_CHECKLIST.md
    echo   2. Schedule penetration testing
    echo   3. Configure monitoring and alerts
    echo.
    exit /b 0
) else (
    echo [31m[ERROR] Found %ISSUES_FOUND% security issue^(s^)[0m
    echo.
    echo Please address the issues above before deploying to production.
    echo Review SECURITY_CHECKLIST.md for detailed mitigation steps.
    echo.
    exit /b 1
)
