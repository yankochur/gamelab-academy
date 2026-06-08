# Gamelab Academy

Учебный REST API проект для академии.

## Установка необходимых инструментов

Перед началом убедись что установлено:

**PHP 8.4+**
- macOS: `brew install php`
- Ubuntu/Debian: `sudo apt install php8.4 php8.4-pgsql php8.4-xml php8.4-mbstring`
- Windows: [windows.php.net/download](https://windows.php.net/download)

**Composer**
- Все платформы: [getcomposer.org/download](https://getcomposer.org/download)

**Docker и Docker Compose**
- Все платформы: [docs.docker.com/get-docker](https://docs.docker.com/get-docker)

**Symfony CLI** (опционально, нужен только для запуска сервера командой `symfony server:start`)
- Все платформы: [symfony.com/download](https://symfony.com/download)

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

Postgres будет доступен на порту **15432**, Redis — на **16379**.

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

## Задание

Твоя задача — реализовать оставшиеся методы в `src/Controller/CurrenciesController.php`:

- `POST /api/currencies` — создание валюты (поля: `name`, `description`, `active`)
- `PUT /api/currencies/{id}` — обновление валюты
- `DELETE /api/currencies/{id}` — удаление валюты

Посмотри на `src/Controller/UsersController.php` как на образец — там реализован полный CRUD.
