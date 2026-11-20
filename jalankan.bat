@echo off
setlocal
cd /d "%~dp0"
set "K3_PHP=php"
where php >nul 2>nul
if errorlevel 1 (
 if exist "C:\xampp\php\php.exe" (set "K3_PHP=C:\xampp\php\php.exe") else if exist "D:\xampp\php\php.exe" (set "K3_PHP=D:\xampp\php\php.exe") else (
  echo PHP tidak ditemukan. Pasang XAMPP terlebih dahulu.
  pause & exit /b 1
 )
)
if not exist ".env" (echo Jalankan setup.bat terlebih dahulu. & pause & exit /b 1)
echo SMART K3 - http://127.0.0.1:8000  ^(biarkan jendela ini terbuka^)
"%K3_PHP%" artisan serve --host=127.0.0.1 --port=8000 --no-reload
pause
