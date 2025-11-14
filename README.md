# DESAFIO-PHP

Sistema de pagamentos com múltiplos gateways, fallback automático e autenticação por roles. Nível 3 do desafio BeTalent.

## O que foi feito

- API em Laravel 10 com Sanctum para autenticação
- Sistema de 2 gateways com fallback automático (se G1 falha, tenta G2)
- Suporte a múltiplos produtos por transação
- Controle de acesso por roles (ADMIN, MANAGER, FINANCE, USER)
- Tudo em Docker (app, MySQL, gateways mock)
- Testes automatizados passando

## Como rodar

### Rápido (recomendado)

Windows:
```powershell
.\setup.bat
```

Linux/Mac:
```bash
bash setup.sh
```

Pronto! Sobe tudo, roda migrations, seeders e testes.

### Manual

```bash
# Copiar env
copy .env.example .env

# Subir Docker
docker compose up --build -d

# Migrations e seeds
docker compose exec app php artisan migrate:fresh --seed --force

# Rodar testes
docker compose exec app php artisan test

# Acessar
http://localhost:8000
```

## Credenciais de teste

| Email | Senha | Role |
|-------|-------|------|
| admin@betalent.tech | password | ADMIN |
| manager@betalent.tech | password | MANAGER |
| finance@betalent.tech | password | FINANCE |
| user@betalent.tech | password | USER |

## Endpoints

### Públicos
- `POST /api/auth/login` - Login
- `POST /api/purchase` - Compra

### Privados (precisa token)
- `PATCH /api/gateways/{id}/toggle` - Liga/desliga gateway
- `PATCH /api/gateways/{id}/priority` - Muda prioridade
- `POST /api/transactions/{id}/refund` - Reembolsa
- `GET /api/transactions` - Lista transações
- `GET /api/transactions/{id}` - Detalhe de transação
- CRUD de products, clients, users

## Testes

```bash
docker compose exec app php artisan test
```

Resultado: 3 testes passando (5 assertions)

## Acessos

| Serviço | Porta |
|---------|-------|
| App | 8000 |
| MySQL | 3307 |
| Gateway 1 | 3001 |
| Gateway 2 | 3002 |

## Gateways

- **Gateway 1** (3001): Autenticação por email + token
- **Gateway 2** (3002): Autenticação por headers

Se um falha, tenta o outro automaticamente (por prioridade).

## Estrutura do projeto

```
├── overlay/app/
│   ├── Http/Controllers/    - Controllers da API
│   ├── Models/              - Models (User, Product, etc)
│   ├── Services/            - PaymentService, GatewayRepository
│   ├── database/
│   │   ├── migrations/      - Schema do banco
│   │   └── seeders/         - Dados iniciais
│   ├── routes/api.php       - Rotas
│   └── tests/               - Testes
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Como funciona o fluxo de pagamento

1. POST em `/api/purchase` com produtos, quantidade e dados do cartão
2. Calcula o total (preço × quantidade)
3. Cria uma transação com status `declined`
4. Tenta cobrar no Gateway 1
5. Se falha, tenta no Gateway 2
6. Se algum funcionar: status vira `approved` (HTTP 201)
7. Se todos falharem: mantém `declined` (HTTP 200)

## Docker

```bash
docker ps                       # Ver containers rodando
docker logs -f desafio-php-app # Ver logs
docker compose exec app bash   # Acessar shell do app
docker compose down -v         # Parar e limpar tudo
```

## Dificuldades encontradas

### Docker + Overlay
**Problema**: Arquivo `artisan` não existia, `composer create-project` falhava porque `/var/www` já tinha arquivos.

**Solução**: Fazer verificação condicional no Dockerfile:
```dockerfile
if [ ! -f /var/www/artisan ]; then
  composer create-project laravel/laravel /tmp/laravel_src
  cp -a /tmp/laravel_src/. /var/www/
fi
```

### Porta MySQL em uso
**Problema**: Porta 3306 já estava em uso.

**Solução**: Mapeou para 3307 no `docker-compose.yml`:
```yaml
ports:
  - "3307:3306"
```

### Migrations duplicadas
**Problema**: Laravel padrão cria migrations `0001_01_01`, overlay tinha `2024_01_01`. Conflito.

**Solução**: Script remove migrations padrão antes de rodar migrations.

### APP_KEY em testes
**Problema**: Testes falhavam sem APP_KEY configurada.

**Solução**: Configurar APP_KEY em runtime no `setUp()` do teste.

### Rotas API não carregavam
**Problema**: `bootstrap/app.php` não registrava rotas de API.

**Solução**: Adicionar `api: __DIR__.'/../routes/api.php'` em `withRouting()`.

## O que foi implementado

### ✅ Implementado tudo

- API com 20 endpoints (públicos + privados)
- Autenticação com Sanctum
- 4 roles diferentes com autorização por gates
- 2 gateways com fallback automático
- Suporte a múltiplos produtos por transação
- Banco de dados com 6 migrations
- Testes automatizados (3 suites passando)
- Docker completo (app + MySQL + gateways mock)
- Setup scripts automáticos (Windows + Linux)

### ❌ Pendente

Nada. Tudo pronto.

## Links

- [Desafio BeTalent](https://github.com/BeMobile/teste-pratico-backend)
- [Laravel 10](https://laravel.com/docs/10.x)
