@echo off
setlocal
cd /d "%~dp0"
echo === Setup SMART K3 (sekali saja setelah clone) ===
set "K3_PHP=php"
where php >nul 2>nul
if errorlevel 1 (
 if exist "C:\xampp\php\php.exe" (set "K3_PHP=C:\xampp\php\php.exe") else if exist "D:\xampp\php\php.exe" (set "K3_PHP=D:\xampp\php\php.exe") else (
  echo PHP tidak ditemukan. Pasang XAMPP ^(PHP 8.2+^) terlebih dahulu.
  pause & exit /b 1
 )
)
if not exist "vendor\autoload.php" (
 where composer >nul 2>nul
 if errorlevel 1 (
  echo Composer belum terpasang. Unduh di https://getcomposer.org/download/ lalu jalankan setup.bat lagi.
  pause & exit /b 1
 )
 call composer install --no-interaction || (pause & exit /b 1)
)
if not exist ".env" copy ".env.example" ".env" >nul
"%K3_PHP%" artisan key:generate --force
if not exist "database\database.sqlite" type nul > "database\database.sqlite"
"%K3_PHP%" artisan migrate --seed --force || (pause & exit /b 1)
echo.
echo Setup selesai. Jalankan "jalankan.bat" lalu buka http://127.0.0.1:8000
pause
