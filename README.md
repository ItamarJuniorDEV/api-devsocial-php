# DevSocial API

> API REST em Laravel 10 para uma rede social: perfis, posts, feed, curtidas, comentários, seguidores e busca, com autenticação por token via Sanctum.

![CI](https://github.com/ItamarJuniorDEV/api-devsocial-php/actions/workflows/ci.yml/badge.svg)
![License](https://img.shields.io/badge/License-MIT-green)

## Índice

- [Sobre](#sobre)
- [Funcionalidades](#funcionalidades)
- [Stack](#stack)
- [Como rodar](#como-rodar)
- [Variáveis de ambiente](#variáveis-de-ambiente)
- [Modelo de dados](#modelo-de-dados)
- [Documentação da API](#documentação-da-api)
- [Testes](#testes)
- [Decisões técnicas](#decisões-técnicas)
- [Licença](#licença)

## Sobre

DevSocial é uma API de rede social no estilo timeline: o usuário se cadastra, monta o perfil, publica posts de texto ou foto, curte e comenta posts dos outros, segue pessoas e tem um feed com quem segue. A busca cobre usuários e posts numa única chamada. O código é o backend; o projeto começou como monorepo com um cliente Vue, mas o frontend foi separado e este repositório mantém só a API.

O foco da implementação está em autenticação segura (login em tempo constante e mensagem de erro uniforme), regra de negócio isolada em Services e respostas com um envelope previsível.

## Funcionalidades

- Cadastro e login com token Bearer (Sanctum)
- Perfil do usuário com avatar, capa, bio e dados pessoais
- Posts de texto e foto, com curtir e comentar
- Seguir e deixar de seguir usuários
- Feed paginado com os posts de quem o usuário segue
- Busca de usuários e posts em uma chamada, paginada
- Rate limiting separado para autenticação e para o restante da API

## Stack

| Camada | Tecnologia |
|--------|------------|
| Linguagem | PHP 8.2 |
| Framework | Laravel 10 |
| Autenticação | Laravel Sanctum (Bearer token) |
| Banco | MySQL 8 (SQLite in-memory nos testes) |
| Documentação | l5-swagger (OpenAPI a partir dos atributos) |
| Testes | PHPUnit 10 |
| Estilo | Laravel Pint |
| Infra | Docker, GitHub Actions |

## Como rodar

Pré-requisitos: PHP 8.2+, Composer, MySQL 8 (ou Docker). O código fica em `backend/`.

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API em `http://localhost:8000/api`.

Com Docker (`backend/docker-compose.yml` sobe app PHP-FPM + nginx + MySQL 8):

```bash
cd backend
docker compose up -d
```

No `.env`, use `DB_HOST=db` (nome do serviço no compose), não `127.0.0.1`.

## Variáveis de ambiente

Principais variáveis do `backend/.env`:

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| `APP_URL` | URL base da aplicação | `http://localhost:8000` |
| `DB_CONNECTION` | Driver do banco | `mysql` |
| `DB_HOST` | Host do banco (serviço do compose) | `db` |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | Credenciais do MySQL | (obrigatória) |
| `QUEUE_CONNECTION` | Driver de fila | `sync` |

## Modelo de dados

- `users` tem muitos `posts`, `post_comments` e curtidas (`post_likes`)
- `posts` pertence a um `user`, tem `type` (`text` ou `photo`) e `body`, com muitos comentários e curtidas
- `post_likes` liga um `user` a um `post` (par único, curtida)
- `post_comments` liga um `user` a um `post` com o texto do comentário
- `user_relations` registra o seguir (`user_from` segue `user_to`, par único)

O perfil em `users` guarda `bio`, `avatar`, `cover`, `birthdate`, `city` e `work`.

## Documentação da API

Todas as rotas exigem `Authorization: Bearer <token>`, exceto registro e login.

| Método | Rota | Acesso | Descrição |
|--------|------|--------|-----------|
| POST | `/api/auth/register` | público | Cria usuário e retorna token |
| POST | `/api/auth/login` | público | Autentica e retorna token |
| GET | `/api/auth/me` | autenticado | Dados do usuário do token |
| POST | `/api/auth/logout` | autenticado | Revoga o token atual |
| GET | `/api/user/me` | autenticado | Perfil completo do usuário |
| PUT | `/api/user/profile` | autenticado | Atualiza dados do perfil |
| POST | `/api/user/avatar` | autenticado | Envia o avatar |
| POST | `/api/user/cover` | autenticado | Envia a capa |
| GET | `/api/feed` | autenticado | Feed paginado de quem o usuário segue |
| POST | `/api/posts` | autenticado | Cria um post |
| POST | `/api/posts/{id}/like` | autenticado | Curte ou descurte um post |
| POST | `/api/posts/{id}/comments` | autenticado | Comenta um post |
| POST | `/api/users/{id}/follow` | autenticado | Segue ou deixa de seguir um usuário |
| GET | `/api/search?q=&per_page=` | autenticado | Busca usuários e posts |

Documentação interativa (l5-swagger) disponível em `/api/documentation`.

### Formato de resposta

Sucesso:

```json
{
  "data": {
    "token": "1|abc...",
    "user": { "id": 1, "name": "Itamar Junior" }
  }
}
```

Listagem paginada (feed, busca) inclui o bloco `meta` com `current_page`, `last_page`, `per_page` e `total`.

Erro de validação (422):

```json
{
  "message": "Os dados fornecidos são inválidos.",
  "errors": { "email": ["O campo email é obrigatório."] }
}
```

## Testes

```bash
cd backend && vendor/bin/phpunit
```

29 testes de feature rodando em SQLite in-memory (não precisa do MySQL configurado). Cobrem autenticação, perfil, posts, curtidas, comentários, seguir, feed e busca.

## Decisões técnicas

- **Login em tempo constante.** O `login` roda dentro de um `Timebox` e a mensagem de erro é uniforme ("Credenciais invalidas"), sem revelar se o e-mail existe. Reduz a margem para timing attack e enumeração de usuários.

- **Regra de negócio em Services.** `AuthService` e `SearchService` concentram a lógica; os controllers só validam a requisição (via Form Requests) e formatam a resposta.

- **Envelope de resposta previsível.** Toda resposta de sucesso vem em `data`; quando há paginação, os totais vêm em `meta`. O cliente sempre lê do mesmo lugar.

- **Throttle separado.** `throttle:auth` mais apertado no registro e login (anti brute-force) e `throttle:api` no restante das rotas autenticadas.

- **Autorização por Policy.** `PostPolicy` controla quem pode alterar um post, bloqueando acesso indevido por ID na URL.

- **Curtir e seguir como toggle.** O mesmo endpoint cria e desfaz a relação, mantendo o par único no banco (`post_likes` e `user_relations`).

## Licença

MIT
