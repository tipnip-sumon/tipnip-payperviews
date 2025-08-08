@echo off
title PayPerViews Production Deployment

echo.
echo ===============================================
echo  PayPerViews Production Deployment Script
echo ===============================================
echo.

REM Check if we're in the right directory
if not exist artisan (
    echo ERROR: artisan file not found. Please run this script from the Laravel root directory.
    pause
    exit /b 1
)

echo [1/10] Setting up environment...
if exist .env.production (
    copy .env.production .env >nul
    echo ✓ Environment file copied from .env.production
) else (
    echo ⚠ WARNING: No .env.production file found. Please configure .env manually.
)

echo.
echo [2/10] Installing Composer dependencies...
call composer install --optimize-autoloader --no-dev --no-interaction
if errorlevel 1 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)

echo.
echo [3/10] Generating application key...
call php artisan key:generate --force

echo.
echo [4/10] Creating required directories...
if not exist storage\logs mkdir storage\logs
if not exist storage\framework\cache mkdir storage\framework\cache
if not exist storage\framework\sessions mkdir storage\framework\sessions
if not exist storage\framework\views mkdir storage\framework\views
if not exist storage\framework\testing mkdir storage\framework\testing
if not exist storage\app\public mkdir storage\app\public
if not exist bootstrap\cache mkdir bootstrap\cache
echo ✓ Required directories created

echo.
echo [5/10] Clearing caches...
call php artisan config:clear
call php artisan cache:clear
call php artisan view:clear
call php artisan route:clear
echo ✓ Caches cleared

echo.
echo [6/10] Database migrations...
set /p migrate="Do you want to run database migrations? (y/N): "
if /i "%migrate%"=="y" (
    call php artisan migrate --force
    echo ✓ Database migrations completed
) else (
    echo ⚠ Skipped database migrations
)

echo.
echo [7/10] Creating storage symlink...
call php artisan storage:link

echo.
echo [8/10] Optimizing for production...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
echo ✓ Production optimization completed

echo.
echo [9/10] Building frontend assets...
where npm >nul 2>nul
if errorlevel 1 (
    echo ⚠ WARNING: npm not found. Please build frontend assets manually with 'npm run build'
) else (
    call npm ci --production
    call npm run build
    echo ✓ Frontend assets built successfully
)

echo.
echo [10/10] Performing final checks...

REM Check if .env exists
if not exist .env (
    echo ERROR: .env file missing!
    pause
    exit /b 1
)

REM Check for APP_KEY
findstr /C:"APP_KEY=base64:" .env >nul
if errorlevel 1 (
    echo ERROR: APP_KEY not properly set in .env file!
    pause
    exit /b 1
)

echo ✓ Final checks completed

echo.
echo ===============================================
echo  Deployment completed successfully! 🎉
echo ===============================================
echo.
echo 📋 Final checklist:
echo    • Configure your web server to point to the 'public' directory
echo    • Update database credentials in .env file
echo    • Update APP_URL in .env file to match your domain
echo    • Set APP_DEBUG=false in .env file
echo    • Set APP_ENV=production in .env file
echo    • Set up SSL certificate
echo    • Test the application thoroughly
echo.
echo 🔗 Important URLs:
echo    • Admin Panel: https://your-domain.com/admin
echo    • Videos Page: https://your-domain.com/videos
echo.
echo ⚠ IMPORTANT REMINDERS:
echo    • Update .env with production database credentials
echo    • Configure mail settings for production
echo    • Set up proper file permissions on Linux/Unix servers
echo    • Configure cron jobs for Laravel scheduler
echo.
pause
