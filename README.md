# Gamelab Academy

Учебный REST API проект для академии.

> **Windows:** все команды выполняй в [WSL](https://learn.microsoft.com/ru-ru/windows/wsl/install) с Ubuntu 24.04. Установи в PowerShell от имени администратора: `wsl --install -d Ubuntu-24.04`, затем перезагрузи компьютер. Версии ниже 24.04 не рекомендуются, версии выше могут вызывать проблемы с пакетами PHP. Mac и Linux — работай напрямую в терминале.

## Установка необходимых инструментов

**PHP 8.4 + Composer**

```bash
sudo apt update
sudo apt install -y php8.4 php8.4-pgsql php8.4-xml php8.4-mbstring php8.4-curl
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

**Docker и Docker Compose**

```bash
sudo apt install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
sudo usermod -aG docker $USER
newgrp docker
```

**Symfony CLI** (опционально, нужен только для `symfony server:start`)

```bash
curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
sudo apt install -y symfony-cli
```

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
