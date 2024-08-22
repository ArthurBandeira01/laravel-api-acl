# ACL API - Laravel 11

## Descrição:

O ACL (Access Control List) API é uma API RESTful com foco em um sistema de controle de acesso que permite definir e gerenciar permissões para diferentes usuários ou grupos de usuários em uma aplicação. O ACL é usado para controlar o que cada usuário ou grupo de usuários pode ou não pode fazer na aplicação.

### Instalação

Clone o repositório

```sh
git clone https://github.com/ArthurBandeira01/laravel-api-acl.git
```

Acesse o diretório do projeto clonado e suba os containers do projeto

```sh
docker-compose up -d
```

Crie o Arquivo .env

```sh
cp .env.example .env
```

Acesse o container app

```sh
docker-compose exec app bash
```

Instale as dependências do projeto
```sh
composer install
```

Gere a key do projeto Laravel
```sh
php artisan key:generate
```

OPCIONAL: Gere o banco SQLite (caso não use o banco MySQL)
```sh
touch database/database.sqlite
```

Rodar as migrations
```sh
php artisan migrate
```

Acesse o projeto
[http://localhost:8000](http://localhost:8000)


## Testes E2E - Pest

Neste projeto optei por usar o Pest por ser um framework simples e fácil de usá-lo. Com ele pude testar todas as rotas de autenticação do usuário, de permissões e de permissões que pertencem ao usuário.

Para baixar o Pest, instale com o composer

```sh
composer remove phpunit/phpunit
composer require pestphp/pest --dev --with-all-dependencies
```

Ele é uma alternativa ao PHPUnit. Porém, caso queira utilizá-lo, manda a ver :)

Para realizar os testes dos endpoints nesse projeto, é importante estar dentro do container e rodar o seguinte como exemplo:

```sh
php artisan test tests/Feature/Api/ACL/ACLApiTest.php
```

Para autenticação usei o Sanctum mesmo, para instalá-lo basta rodar esse comando abaixo
```sh
php artisan install:api
```


