@echo off
setlocal EnableDelayedExpansion

:: Prompt user for repository URL and target directory
set /p target_dir="Enter the target directory name: "

:: Clone the repository (develop branch only)
echo Cloning the develop branch...
git clone --branch develop --single-branch https://github.com/SyamStud/INVENTORY-SCI.git %target_dir%
if errorlevel 1 goto error

:: Navigate to the project directory
cd %target_dir%
if errorlevel 1 goto error

:: Install PHP dependencies
echo Running composer update...
call composer update
if errorlevel 1 goto error

:: Install Node dependencies
echo Running node update...
call npm install
if errorlevel 1 goto error

:: Build assets
echo Building assets...
call npm run build
if errorlevel 1 goto error

:: Get database configuration from user
set /p DB_DATABASE="Enter database name (press Enter for 'inventory_sci'): "
if "!DB_DATABASE!"=="" set DB_DATABASE=inventory_sci
set /p DB_USERNAME="Enter database username: "
set /p DB_PASSWORD="Enter database password: "
set /p DB_HOST="Enter database host (press Enter for 'localhost'): "
if "!DB_HOST!"=="" set DB_HOST=127.0.0.1

:: Create a new .env file and configure database
echo Creating and configuring .env file...
copy .env.example .env
if errorlevel 1 goto error

:: Update database configuration in .env file
powershell -Command "(Get-Content .env) | ForEach-Object { $_ -replace '^DB_HOST=.*$', 'DB_HOST=!DB_HOST!' -replace '^DB_DATABASE=.*$', 'DB_DATABASE=!DB_DATABASE!' -replace '^DB_USERNAME=.*$', 'DB_USERNAME=!DB_USERNAME!' -replace '^DB_PASSWORD=.*$', 'DB_PASSWORD=!DB_PASSWORD!' } | Set-Content .env"
if errorlevel 1 goto error

:: Generate application key
echo Generating application key...
php artisan key:generate
if errorlevel 1 goto error

:: Set storage permissions
echo Linking storage...
php artisan storage:link
if errorlevel 1 goto error

:: Migrate database
echo Running migrations...
php artisan db:create
if errorlevel 1 goto error

:: Seed database
echo Seeding database...
php artisan db:seed
if errorlevel 1 goto error

:: Clear and cache configurations
echo Clearing configurations...
php artisan config:clear
if errorlevel 1 goto error

:: Docker setup
echo Setting up Docker...
docker run --rm -d --restart unless-stopped -p 3000:3000 gotenberg/gotenberg:8
if errorlevel 1 goto error

:: Completion message
echo Project setup completed!
pause
exit /b 0

:error
echo An error occurred during the setup process.
echo Error occurred in: %CD%
pause
exit /b 1