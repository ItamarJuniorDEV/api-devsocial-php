# DevSocial API

Uma API RESTful para rede social de desenvolvedores, com recursos para autenticação, perfil, posts, feed personalizado, seguidores e busca.

## Funcionalidades

- Autenticação e registro de usuários com token via Sanctum
- Gerenciamento de perfil (atualização de dados, avatar e capa)
- Posts de texto e foto (criação, curtidas e comentários)
- Feed personalizado com posts próprios e de usuários seguidos
- Seguir e deixar de seguir outros usuários
- Busca de usuários e posts por termo

## Tecnologias

- PHP 8.2
- Laravel 10
- Laravel Sanctum (autenticação)
- MySQL 8.0
- Docker (PHP-FPM, Nginx, MySQL)
- PHPUnit (testes)
- Swagger / l5-swagger (documentação)

## Instalação

```bash
# Clone o repositório
git clone https://github.com/ItamarJuniorDEV/devsocial.git

# Entre na pasta do projeto
cd devsocial

# Suba os containers
docker-compose up -d

# Gere a chave da aplicação
docker-compose exec app php artisan key:generate

# Execute as migrações
docker-compose exec app php artisan migrate

# Crie o link do storage
docker-compose exec app php artisan storage:link
```

## Documentação da API

A documentação completa da API está disponível através do Swagger UI.

Para acessar, suba o servidor e acesse: [Swagger UI](http://localhost:8000/api/documentation)

## Endpoints

| Recurso      | Método  | Endpoint                        | Descrição                              |
|-------------|---------|--------------------------------|----------------------------------------|
| Auth         | `POST`  | `/api/auth/register`           | Registrar novo usuário                 |
| Auth         | `POST`  | `/api/auth/login`              | Autenticar e obter token               |
| Auth         | `GET`   | `/api/auth/me`                 | Dados do usuário autenticado           |
| Auth         | `POST`  | `/api/auth/logout`             | Encerrar sessão                        |
| Usuário      | `GET`   | `/api/user/me`                 | Perfil do usuário autenticado          |
| Usuário      | `PUT`   | `/api/user/profile`            | Atualizar dados do perfil              |
| Usuário      | `POST`  | `/api/user/avatar`             | Upload de avatar                       |
| Usuário      | `POST`  | `/api/user/cover`              | Upload de capa                         |
| Feed         | `GET`   | `/api/feed`                    | Listar feed paginado                   |
| Posts        | `POST`  | `/api/posts`                   | Criar post de texto ou foto            |
| Posts        | `POST`  | `/api/posts/{id}/like`         | Curtir ou descurtir post               |
| Posts        | `POST`  | `/api/posts/{id}/comments`     | Comentar em um post                    |
| Seguidores   | `POST`  | `/api/users/{id}/follow`       | Seguir ou deixar de seguir usuário     |
| Busca        | `GET`   | `/api/search?q=`               | Buscar usuários e posts                |

## Testes

```bash
# Rodar todos os testes
docker-compose exec app php artisan test

# Ou diretamente com PHPUnit
docker-compose exec app vendor/bin/phpunit
```

## Licença

Este projeto está licenciado sob a licença MIT.

## Autor

Itamar Alves Ferreira Junior
