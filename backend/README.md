# DevSocial Backend

API REST em Laravel 10 com autenticação Sanctum e MySQL 8. Visão geral e decisões técnicas no [README da raiz](../README.md).

## Stack

- PHP 8.2+
- Laravel 10
- Sanctum (bearer tokens)
- MySQL 8 (SQLite in-memory nos testes)

## Como rodar (sem Docker)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

API em `http://localhost:8000`.

## Como rodar (com Docker)

```bash
docker compose up -d
```

Sobe app + nginx + MySQL.

## Endpoints

Públicos:

- POST `/api/auth/register`
- POST `/api/auth/login`

Autenticados (`Authorization: Bearer <token>`):

- GET  `/api/auth/me`
- POST `/api/auth/logout`
- GET  `/api/user/me`
- PUT  `/api/user/profile`
- POST `/api/user/avatar`
- POST `/api/user/cover`
- GET  `/api/feed`
- POST `/api/posts`
- POST `/api/posts/{id}/like`
- POST `/api/posts/{id}/comments`
- POST `/api/users/{id}/follow`
- GET  `/api/search?q=&per_page=`

Documentação Swagger em `/api/documentation`.

## Testes

```bash
vendor/bin/phpunit
```
