# Gamelab Academy Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Создать учебный Symfony REST API проект с двумя модулями (Users, Currencies), Docker Compose для Postgres/Redis, и миграциями — готовый к клонированию новичком.

**Architecture:** Symfony 7 skeleton без сервисного слоя — контроллер обращается к репозиторию напрямую. Users — полный CRUD. Currencies — GET рабочие, write-методы как TODO для новичка.

**Tech Stack:** PHP 8.2+, Symfony 7, Doctrine ORM, Doctrine Migrations, PostgreSQL, Redis (predis), Docker Compose.

---

## File Map

| Файл | Действие | Ответственность |
|------|----------|-----------------|
| `composer.json` | Create | Зависимости Symfony skeleton |
| `docker-compose.yml` | Create | Postgres + Redis сервисы |
| `.env.example` | Create | Шаблон переменных окружения |
| `.env` | Create | Локальные переменные (в .gitignore) |
| `.gitignore` | Create | Игнор .env, vendor, var |
| `config/packages/doctrine.yaml` | Create | Doctrine конфигурация |
| `config/packages/framework.yaml` | Create | Symfony framework конфиг |
| `config/routes.yaml` | Create | Роутинг |
| `src/Entity/User.php` | Create | Doctrine entity для users |
| `src/Entity/Currency.php` | Create | Doctrine entity для currencies |
| `src/Repository/UserRepository.php` | Create | Запросы к таблице users |
| `src/Repository/CurrencyRepository.php` | Create | Запросы к таблице currencies |
| `src/Controller/UsersController.php` | Create | CRUD эндпоинты для users |
| `src/Controller/CurrenciesController.php` | Create | GET рабочие, write — TODO |
| `migrations/Version...Users.php` | Generate | Миграция таблицы users |
| `migrations/Version...Currencies.php` | Generate | Миграция таблицы currencies |
| `README.md` | Create | Инструкция для новичка |

---

### Task 1: Инициализация Symfony проекта

**Files:**
- Create: `composer.json`
- Create: `.gitignore`
- Create: `.env.example`
- Create: `.env`

- [ ] **Step 1: Создать Symfony skeleton**

```bash
cd /home/yankochur/projects/gamelab-academy
composer create-project symfony/skeleton . --no-interaction
```

Ожидаемый результат: появятся `bin/`, `config/`, `public/`, `src/`, `var/`, `vendor/`, `composer.json`, `.env`.

- [ ] **Step 2: Установить необходимые пакеты**

```bash
composer require doctrine/doctrine-bundle doctrine/doctrine-migrations-bundle doctrine/orm symfony/serializer symfony/validator predis/predis symfony/maker-bundle --dev
```

- [ ] **Step 3: Обновить .gitignore**

Убедиться что в `.gitignore` есть:
```
/.env
/vendor/
/var/
```

- [ ] **Step 4: Создать .env.example**

```bash
cp .env .env.example
```

Отредактировать `.env.example` — заменить реальные значения на плейсхолдеры:
```
DATABASE_URL="postgresql://academy_user:academy_pass@127.0.0.1:5432/academy_db?serverVersion=15&charset=utf8"
REDIS_URL="redis://localhost:6379"
APP_ENV=dev
APP_SECRET=change_me_to_random_32_char_string
```

- [ ] **Step 5: Настроить .env для локальной работы**

`.env` должен содержать те же значения что и `.env.example` — они соответствуют docker-compose сервисам которые создадим в Task 2.

- [ ] **Step 6: Commit**

```bash
git add composer.json composer.lock symfony.lock .gitignore .env.example config/ bin/ public/ src/
git commit -m "feat: init symfony skeleton with doctrine and predis"
```

---

### Task 2: Docker Compose для Postgres и Redis

**Files:**
- Create: `docker-compose.yml`

- [ ] **Step 1: Создать docker-compose.yml**

```yaml
version: '3.8'

services:
  postgres:
    image: postgres:15
    environment:
      POSTGRES_DB: academy_db
      POSTGRES_USER: academy_user
      POSTGRES_PASSWORD: academy_pass
    ports:
      - "5432:5432"
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7
    ports:
      - "6379:6379"

volumes:
  postgres_data:
```

- [ ] **Step 2: Проверить что контейнеры поднимаются**

```bash
docker-compose up -d
docker-compose ps
```

Ожидаемый результат: оба сервиса в статусе `Up`.

- [ ] **Step 3: Commit**

```bash
git add docker-compose.yml
git commit -m "feat: add docker-compose for postgres and redis"
```

---

### Task 3: Doctrine конфигурация

**Files:**
- Modify: `config/packages/doctrine.yaml`

- [ ] **Step 1: Настроить doctrine.yaml**

Содержимое `config/packages/doctrine.yaml`:
```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        types:
            uuid: Symfony\Bridge\Doctrine\Types\UuidType
    orm:
        auto_generate_proxy_classes: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App

doctrine_migrations:
    migrations_paths:
        'DoctrineMigrations': '%kernel.project_dir%/migrations'
    enable_profiler: false
```

- [ ] **Step 2: Проверить соединение с БД**

```bash
php bin/console doctrine:database:create
```

Ожидаемый результат: `Created database "academy_db" for connection named default`

- [ ] **Step 3: Commit**

```bash
git add config/packages/doctrine.yaml
git commit -m "feat: configure doctrine orm and migrations"
```

---

### Task 4: Entity User и миграция

**Files:**
- Create: `src/Entity/User.php`
- Create: `src/Repository/UserRepository.php`
- Generate: `migrations/Version...php`

- [ ] **Step 1: Создать Entity User**

```php
<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private string $lastname;

    #[ORM\Column(type: 'integer')]
    private int $age;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $cdate;

    public function getId(): ?int { return $this->id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getFirstname(): string { return $this->firstname; }
    public function setFirstname(string $firstname): static { $this->firstname = $firstname; return $this; }

    public function getLastname(): string { return $this->lastname; }
    public function setLastname(string $lastname): static { $this->lastname = $lastname; return $this; }

    public function getAge(): int { return $this->age; }
    public function setAge(int $age): static { $this->age = $age; return $this; }

    public function getCdate(): \DateTimeInterface { return $this->cdate; }
    public function setCdate(\DateTimeInterface $cdate): static { $this->cdate = $cdate; return $this; }
}
```

- [ ] **Step 2: Создать UserRepository**

```php
<?php
// src/Repository/UserRepository.php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAll(): array
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
```

- [ ] **Step 3: Сгенерировать миграцию**

```bash
php bin/console doctrine:migrations:diff
```

Ожидаемый результат: создан файл `migrations/Version<timestamp>.php` с `CREATE TABLE users`.

- [ ] **Step 4: Проверить миграцию и применить**

```bash
php bin/console doctrine:migrations:migrate --no-interaction
```

Ожидаемый результат: `[OK] Successfully executed 1 migrations.`

- [ ] **Step 5: Commit**

```bash
git add src/Entity/User.php src/Repository/UserRepository.php migrations/
git commit -m "feat: add User entity and migration"
```

---

### Task 5: Entity Currency и миграция

**Files:**
- Create: `src/Entity/Currency.php`
- Create: `src/Repository/CurrencyRepository.php`
- Generate: `migrations/Version...php`

- [ ] **Step 1: Создать Entity Currency**

```php
<?php
// src/Entity/Currency.php
namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table(name: 'currencies')]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $cdate;

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function isActive(): bool { return $this->active; }
    public function setActive(bool $active): static { $this->active = $active; return $this; }

    public function getCdate(): \DateTimeInterface { return $this->cdate; }
    public function setCdate(\DateTimeInterface $cdate): static { $this->cdate = $cdate; return $this; }
}
```

- [ ] **Step 2: Создать CurrencyRepository**

```php
<?php
// src/Repository/CurrencyRepository.php
namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    public function findAll(): array
    {
        return $this->findBy([], ['id' => 'ASC']);
    }
}
```

- [ ] **Step 3: Сгенерировать и применить миграцию**

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate --no-interaction
```

Ожидаемый результат: создан и применён файл миграции с `CREATE TABLE currencies`.

- [ ] **Step 4: Commit**

```bash
git add src/Entity/Currency.php src/Repository/CurrencyRepository.php migrations/
git commit -m "feat: add Currency entity and migration"
```

---

### Task 6: UsersController — полный CRUD

**Files:**
- Create: `src/Controller/UsersController.php`

- [ ] **Step 1: Создать UsersController**

```php
<?php
// src/Controller/UsersController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/users')]
class UsersController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        return $this->json(array_map(fn(User $u) => $this->serialize($u), $users));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serialize($user));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['firstname']) || empty($data['lastname']) || !isset($data['age'])) {
            return $this->json(['error' => 'Fields required: email, firstname, lastname, age'], Response::HTTP_BAD_REQUEST);
        }

        $user = (new User())
            ->setEmail($data['email'])
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setAge((int) $data['age'])
            ->setCdate(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        return $this->json($this->serialize($user), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) $user->setEmail($data['email']);
        if (isset($data['firstname'])) $user->setFirstname($data['firstname']);
        if (isset($data['lastname'])) $user->setLastname($data['lastname']);
        if (isset($data['age'])) $user->setAge((int) $data['age']);

        $this->em->flush();

        return $this->json($this->serialize($user));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serialize(User $user): array
    {
        return [
            'id'        => $user->getId(),
            'email'     => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname'  => $user->getLastname(),
            'age'       => $user->getAge(),
            'cdate'     => $user->getCdate()->format(\DateTime::ATOM),
        ];
    }
}
```

- [ ] **Step 2: Проверить что маршруты зарегистрированы**

```bash
php bin/console debug:router | grep users
```

Ожидаемый результат: 5 строк с маршрутами `GET /api/users`, `GET /api/users/{id}`, `POST /api/users`, `PUT /api/users/{id}`, `DELETE /api/users/{id}`.

- [ ] **Step 3: Запустить сервер и проверить вручную**

```bash
symfony server:start -d
curl -s http://localhost:8000/api/users | python3 -m json.tool
```

Ожидаемый результат: `[]` (пустой массив).

```bash
curl -s -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","firstname":"Ivan","lastname":"Petrov","age":25}' | python3 -m json.tool
```

Ожидаемый результат: JSON с созданным пользователем и `id: 1`.

- [ ] **Step 4: Commit**

```bash
git add src/Controller/UsersController.php
git commit -m "feat: add UsersController with full CRUD"
```

---

### Task 7: CurrenciesController — GET рабочие, write TODO

**Files:**
- Create: `src/Controller/CurrenciesController.php`

- [ ] **Step 1: Создать CurrenciesController**

```php
<?php
// src/Controller/CurrenciesController.php
namespace App\Controller;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/currencies')]
class CurrenciesController extends AbstractController
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private EntityManagerInterface $em,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $currencies = $this->currencyRepository->findAll();

        return $this->json(array_map(fn(Currency $c) => $this->serialize($c), $currencies));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $currency = $this->currencyRepository->find($id);
        if (!$currency) {
            return $this->json(['error' => 'Currency not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serialize($currency));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // TODO: реализовать создание валюты
        // Подсказка: посмотри как устроен UsersController::create()
        // Поля: name (string, обязательное), description (string, опциональное), active (bool, default true)
        // Не забудь установить cdate = new \DateTime()
        return $this->json(['error' => 'Not implemented'], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        // TODO: реализовать обновление валюты
        // Подсказка: посмотри как устроен UsersController::update()
        // Можно обновлять поля: name, description, active
        return $this->json(['error' => 'Not implemented'], Response::HTTP_NOT_IMPLEMENTED);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        // TODO: реализовать удаление валюты
        // Подсказка: посмотри как устроен UsersController::delete()
        return $this->json(['error' => 'Not implemented'], Response::HTTP_NOT_IMPLEMENTED);
    }

    private function serialize(Currency $currency): array
    {
        return [
            'id'          => $currency->getId(),
            'name'        => $currency->getName(),
            'description' => $currency->getDescription(),
            'active'      => $currency->isActive(),
            'cdate'       => $currency->getCdate()->format(\DateTime::ATOM),
        ];
    }
}
```

- [ ] **Step 2: Проверить маршруты**

```bash
php bin/console debug:router | grep currencies
```

Ожидаемый результат: 5 строк с маршрутами currencies.

- [ ] **Step 3: Проверить рабочие GET-эндпоинты**

```bash
curl -s http://localhost:8000/api/currencies | python3 -m json.tool
```

Ожидаемый результат: `[]`.

```bash
curl -s -X POST http://localhost:8000/api/currencies \
  -H "Content-Type: application/json" \
  -d '{"name":"Dollar"}' | python3 -m json.tool
```

Ожидаемый результат: `{"error": "Not implemented"}` со статусом 501.

- [ ] **Step 4: Commit**

```bash
git add src/Controller/CurrenciesController.php
git commit -m "feat: add CurrenciesController with GET endpoints and TODO stubs"
```

---

### Task 8: README для новичка

**Files:**
- Create: `README.md`

- [ ] **Step 1: Создать README.md**

```markdown
# Gamelab Academy

Учебный REST API проект для академии программистов.

## Требования

- PHP 8.2+
- Composer
- Docker и Docker Compose
- [Symfony CLI](https://symfony.com/download) (опционально)

## Быстрый старт

**1. Клонировать репозиторий**
```bash
git clone <repo-url>
cd gamelab-academy
```

**2. Поднять Postgres и Redis через Docker**
```bash
docker-compose up -d
```

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

| Метод  | URL              | Описание               |
|--------|------------------|------------------------|
| GET    | /api/users       | Список всех пользователей |
| GET    | /api/users/{id}  | Получить пользователя  |
| POST   | /api/users       | Создать пользователя   |
| PUT    | /api/users/{id}  | Обновить пользователя  |
| DELETE | /api/users/{id}  | Удалить пользователя   |

Пример создания пользователя:
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"email":"ivan@example.com","firstname":"Ivan","lastname":"Petrov","age":25}'
```

### Currencies

| Метод  | URL                   | Описание              |
|--------|-----------------------|-----------------------|
| GET    | /api/currencies       | Список валют          |
| GET    | /api/currencies/{id}  | Получить валюту       |
| POST   | /api/currencies       | **TODO** — реализовать |
| PUT    | /api/currencies/{id}  | **TODO** — реализовать |
| DELETE | /api/currencies/{id}  | **TODO** — реализовать |

## Задание

Твоя задача — реализовать оставшиеся методы в `src/Controller/CurrenciesController.php`:

- `POST /api/currencies` — создание валюты (поля: `name`, `description`, `active`)
- `PUT /api/currencies/{id}` — обновление валюты
- `DELETE /api/currencies/{id}` — удаление валюты

Посмотри на `src/Controller/UsersController.php` как на образец — там реализован полный CRUD.
```

- [ ] **Step 2: Commit**

```bash
git add README.md
git commit -m "docs: add README with setup instructions and task description"
```

---

### Task 9: Финальная проверка

- [ ] **Step 1: Убедиться что всё чисто**

```bash
php bin/console cache:clear
php bin/console debug:router
```

Ожидаемый результат: 10 маршрутов — 5 для users, 5 для currencies.

- [ ] **Step 2: Прогнать полный smoke-test**

```bash
# Создать пользователя
curl -s -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -d '{"email":"smoke@test.com","firstname":"Test","lastname":"User","age":30}' | python3 -m json.tool

# Получить список
curl -s http://localhost:8000/api/users | python3 -m json.tool

# Получить по id
curl -s http://localhost:8000/api/users/1 | python3 -m json.tool

# Обновить
curl -s -X PUT http://localhost:8000/api/users/1 \
  -H "Content-Type: application/json" \
  -d '{"age":31}' | python3 -m json.tool

# Получить список currencies (пустой)
curl -s http://localhost:8000/api/currencies | python3 -m json.tool

# Проверить что POST currencies возвращает 501
curl -s -o /dev/null -w "%{http_code}" -X POST http://localhost:8000/api/currencies \
  -H "Content-Type: application/json" \
  -d '{"name":"Dollar"}'
```

Ожидаемые результаты: все GET возвращают 200, POST users — 201, PUT users — 200, POST currencies — 501.

- [ ] **Step 3: Удалить тестового пользователя**

```bash
curl -s -X DELETE http://localhost:8000/api/users/1
```

Ожидаемый результат: статус 204, пустое тело.

- [ ] **Step 4: Финальный commit**

```bash
git add -A
git commit -m "chore: final cleanup"
```
