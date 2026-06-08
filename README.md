# Gamelab Academy

Учебный REST API проект для академии.

## Требования

- PHP 8.2+
- Composer
- Docker и Docker Compose
- [Symfony CLI](https://symfony.com/download) (опционально)

## Быстрый старт

**1. Клонировать репозиторий**

```bash
git clone https://github.com/yankochur/gamelab-academy.git
cd gamelab-academy
```

**2. Поднять Postgres и Redis через Docker**

```bash
docker compose up -d
```

Postgres будет доступен на порту **15432**, Redis — на **16379**

**3. Установить PHP-зависимости**

```bash
composer install
```

**4. Настроить окружение**

```bash
cp .env.example .env
```

Значения в `.env` уже настроены под Docker Compose — менять ничего не нужно.

**5. Создать базу данных и накатить миграции**

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

**6. Запустить сервер**

```bash
symfony server:start
# или без Symfony CLI:
php -S localhost:8000 -t public/
```

API доступен на `http://localhost:8000`.

## Эндпоинты

### Users (полный CRUD)

| Метод  | URL               | Описание                  |
|--------|-------------------|---------------------------|
| GET    | /api/users        | Список всех пользователей |
| GET    | /api/users/{id}   | Получить пользователя     |
| POST   | /api/users        | Создать пользователя      |
| PUT    | /api/users/{id}   | Обновить пользователя     |
| DELETE | /api/users/{id}   | Удалить пользователя      |

Пример создания пользователя:

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"email":"ivan@example.com","firstname":"Ivan","lastname":"Petrov","age":25}'
```

### Currencies
| Метод  | URL                    | Описание               |
|--------|------------------------|------------------------|
| GET    | /api/currencies        | Список валют           |
| GET    | /api/currencies/{id}   | Получить валюту        |
| POST   | /api/currencies        | **TODO** — реализовать |
| PUT    | /api/currencies/{id}   | **TODO** — реализовать |
| DELETE | /api/currencies/{id}   | **TODO** — реализовать |

