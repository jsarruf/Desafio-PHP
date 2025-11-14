#!/bin/bash
# DESAFIO-PHP - Setup Script para Linux/Mac
# Este script configura e executa o projeto automaticamente

set -e

echo ""
echo "========================================"
echo "DESAFIO-PHP - Setup Automatico"
echo "========================================"
echo ""

# Verificar se Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "[ERRO] Docker nao encontrado. Instale Docker Desktop primeiro."
    echo "Baixe em: https://www.docker.com/products/docker-desktop"
    exit 1
fi

echo "[OK] Docker encontrado."
echo ""

# Copiar .env.example para .env se nao existir
if [ ! -f .env ]; then
    echo "[CRIANDO] .env a partir de .env.example..."
    cp .env.example .env
    echo "[OK] .env criado."
else
    echo "[OK] .env ja existe."
fi

echo ""
echo "[INICIANDO] Containers Docker..."
docker compose up --build -d

echo ""
echo "[AGUARDANDO] MySQL iniciar (10 segundos)..."
sleep 10

echo ""
echo "[EXECUTANDO] Migrations e Seeders..."
docker compose exec app bash -lc "rm -f database/migrations/0001_01_01_* && php artisan migrate:fresh --seed --force"

echo ""
echo "[COPIANDO] Arquivos de configuracao para o container..."
docker cp tests/Feature/ExampleTest.php desafio-php-app:/var/www/tests/Feature/ExampleTest.php 2>/dev/null || true
docker cp bootstrap/app.php desafio-php-app:/var/www/bootstrap/app.php 2>/dev/null || true
docker cp overlay/app/Providers/RouteServiceProvider.php desafio-php-app:/var/www/app/Providers/RouteServiceProvider.php 2>/dev/null || true

echo ""
echo "[EXECUTANDO] Testes..."
docker compose exec app php artisan test

echo ""
echo "========================================"
echo "[SUCESSO] Projeto pronto!"
echo "========================================"
echo ""
echo "Acesse:"
echo "  App: http://localhost:8000"
echo "  API: http://localhost:8000/api"
echo ""
echo "Credenciais de teste:"
echo "  admin@betalent.tech / password (ADMIN)"
echo "  manager@betalent.tech / password (MANAGER)"
echo "  finance@betalent.tech / password (FINANCE)"
echo "  user@betalent.tech / password (USER)"
echo ""
echo "Para parar os containers: docker compose down"
echo "Para ver logs: docker logs -f desafio-php-app"
echo ""
