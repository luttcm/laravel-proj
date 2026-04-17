# Calcul

Внутреннее Laravel-приложение для расчётов сделок, финансовой отчётности и операционной работы по ролям.

## Что делает система

- расчёт сделок для менеджеров (`/managers`) с сохранением черновиков и финальных отчётов;
- блок финансового директора (`/findirector/fin-reports`) для ведения фин. отчётов;
- управление справочниками: НДС, СПК, поставщики, компании-источники;
- новости с лайками и комментариями;
- база знаний с категориями и загрузкой изображений через CKEditor;
- управление пользователями и профилем;
- веб-аутентификация и 2FA;
- API-аутентификация на JWT (`/api/*`).

## Технологии

- PHP 8.1+ (в Docker используется PHP 8.2)
- Laravel 10
- MySQL 8
- Redis 7
- Vite (frontend сборка)
- PHPUnit, Larastan (PHPStan), L5 Swagger

## Структура репозитория

- корень репозитория: Docker, инфраструктура, документация;
- приложение Laravel: `public_html/app` (этот каталог).

## Быстрый старт (Docker, рекомендовано)

Выполнять из корня репозитория.

```bash
docker compose up -d --build
```

После запуска:

- приложение: `http://localhost`
- phpMyAdmin: `http://localhost:8001`

Инициализация Laravel (первый запуск):

```bash
docker compose exec app bash -lc "cd /var/www/html/app && cp -n .env.example .env && composer install && php artisan key:generate && php artisan migrate --force"
```

## Локальный запуск без Docker

Выполнять из `public_html/app`.

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

## Команды разработки

```bash
# тесты
php artisan test

# статический анализ
composer phpstan

# генерация swagger
composer swagger

# сборка фронта
npm run build
```

## Роли и доступ

Используются роли: `admin`, `manager`, `finance`, `redactor`, `user`.

Ключевые ограничения:

- админ/менеджер/финансы: пользователи и справочники;
- админ/редактор: управление новостями;
- админ/финансы: блок финансового директора.

## API auth (кратко)

- `POST /api/register`
- `POST /api/auth`
- `GET /api/me` (JWT)
- `POST /api/logout` (JWT)
- `POST /api/refresh` (JWT)

## Дополнительная документация

- деплой и прод-окружение: `../../DEPLOY.md`
- история изменений проекта: `../../CHANGELOG.md`
- документация по логину/сессиям: `../../LOGIN_DOCUMENTATION.md`
