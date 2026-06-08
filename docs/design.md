# Первые шаги

**Date:** 2026-06-08  


## Overview

На первом этапе необходимо клонировать проект, поднять БД и Redis через Docker и поставить PHP-зависимости. Проект служит базой для тестовых заданий (CRUD, кэширование, новые модули).

## Стек

- PHP 8.2+ / Symfony 7
- Doctrine ORM + Doctrine Migrations
- PostgreSQL (через Docker Compose)
- Redis (через Docker Compose)
- Без фронтенда, без аутентификации

## Структура

```
gamelab-academy/
├── docker-compose.yml
├── .env.example
├── .env                        # в .gitignore
├── composer.json
├── bin/console
├── config/
├── migrations/
│   ├── Version...Users.php
│   └── Version...Currencies.php
└── src/
    ├── Controller/
    │   ├── UsersController.php
    │   └── CurrenciesController.php
    ├── Entity/
    │   ├── User.php
    │   └── Currency.php
    └── Repository/
        ├── UserRepository.php
        └── CurrencyRepository.php
```

! Контроллер обращается к репозиторию напрямую.

## Модели

### Table `users`

| поле      | тип                    |
|-----------|------------------------|
| id        | int, PK, auto          |
| email     | varchar, unique        |
| firstname | varchar                |
| lastname  | varchar                |
| age       | int                    |
| cdate     | datetime               |

### Table `currencies`

| поле        | тип                        |
|-------------|----------------------------|
| id          | int, PK, auto              |
| name        | varchar                    |
| description | text, nullable             |
| active      | boolean, default true      |
| cdate       | datetime                   |

## API Endpoints

### Users (полный CRUD)

```
GET    /api/users
GET    /api/users/{id}
POST   /api/users
PUT    /api/users/{id}
DELETE /api/users/{id}
```

### Currencies (частичный

```
GET    /api/currencies        # рабочий
GET    /api/currencies/{id}   # рабочий
POST   /api/currencies        # TODO
PUT    /api/currencies/{id}   # TODO
DELETE /api/currencies/{id}   # TODO
```

## Настройка окружения

```bash
git clone <repo>
cd gamelab-academy

# Поднять Postgres и Redis
docker-compose up -d

# Установить PHP-зависимости
composer install

# Настроить окружение
cp .env.example .env

# Создать БД и накатить миграции
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Запустить сервер
symfony server:start
# или: php -S localhost:8000 -t public/
```

API доступен на `http://localhost:8000`.

## Redis

Подключение настроено через `.env` (`REDIS_URL`). В текущей версии активно не используется.
