@echo off
REM DESAFIO-PHP - Setup Script para Windows
REM Este script configura e executa o projeto automaticamente

echo.
echo ========================================
echo DESAFIO-PHP - Setup Automatico
echo ========================================
echo.

REM Verificar se Docker está instalado
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERRO] Docker nao encontrado. Instale Docker Desktop primeiro.
    echo Baixe em: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

echo [OK] Docker encontrado.
echo.

REM Copiar .env.example para .env se nao existir
if not exist .env (
    echo [CRIANDO] .env a partir de .env.example...
    copy .env.example .env >nul
    echo [OK] .env criado.
) else (
    echo [OK] .env ja existe.
)

echo.
echo [INICIANDO] Containers Docker...
docker compose up --build -d

echo.
echo [AGUARDANDO] MySQL iniciar (10 segundos)...
timeout /t 10 /nobreak

echo.
echo [EXECUTANDO] Migrations e Seeders...
docker compose exec app bash -lc "rm -f database/migrations/0001_01_01_* && php artisan migrate:fresh --seed --force"

echo.
echo [COPIANDO] Arquivos de configuracao para o container...
docker cp tests\Feature\ExampleTest.php desafio-php-app:/var/www/tests/Feature/ExampleTest.php >nul 2>&1
docker cp bootstrap\app.php desafio-php-app:/var/www/bootstrap/app.php >nul 2>&1
docker cp overlay\app\Providers\RouteServiceProvider.php desafio-php-app:/var/www/app/Providers/RouteServiceProvider.php >nul 2>&1

echo.
echo [EXECUTANDO] Testes...
docker compose exec app php artisan test

echo.
echo ========================================
echo [SUCESSO] Projeto pronto!
echo ========================================
echo.
echo Acesse:
echo   App: http://localhost:8000
echo   API: http://localhost:8000/api
echo.
echo Credenciais de teste:
echo   admin@betalent.tech / password (ADMIN)
echo   manager@betalent.tech / password (MANAGER)
echo   finance@betalent.tech / password (FINANCE)
echo   user@betalent.tech / password (USER)
echo.
echo Para parar os containers: docker compose down
echo Para ver logs: docker logs -f desafio-php-app
echo.
pause
