@echo off
SET PHPRC=C:\Users\juan_
SET PATH=C:\Program Files\php 8.5.5;%PATH%
echo =====================================================
echo  Bolsa de Empleo UNIPAZ - Servidor de desarrollo
echo =====================================================
echo.
echo Iniciando servidor en http://127.0.0.1:8000
echo Presiona Ctrl+C para detener el servidor.
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause
