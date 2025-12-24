# Документация по системе входа (Login)

## 📋 Обзор

Система входа реализована без регистрации. Пользователи могут входить в систему, используя email и пароль. Регистрация новых пользователей будет реализована отдельно.

## 🔑 Учетные данные для демо

```
Email: test@test.com
Пароль: password
```

## 🎨 Страница входа

### Адрес
- **WEB:** `/login` (GET, POST)
- **API:** `/api/login` (POST)

### Файлы

#### View
- `resources/views/auth/login.blade.php` - Главная страница входа

**Особенности дизайна:**
- Красивый градиентный фон (фиолетовый → розовый)
- Адаптивный дизайн (мобильные и десктопные устройства)
- Демо учетные данные выведены на странице
- Красивые input поля с фокусировкой
- Обработка ошибок с красивым оформлением

#### Контроллер
- `app/Http/Controllers/AuthController.php`

**Методы для веб-аутентификации:**
- `loginView()` - Показать форму входа
- `webLogin()` - Обработать отправку формы входа
- `webLogout()` - Выход пользователя

#### Маршруты (routes/web.php)

```php
// Для неавторизованных пользователей (middleware 'guest')
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login', [AuthController::class, 'webLogin']);

// Для авторизованных пользователей
Route::post('/logout', [AuthController::class, 'webLogout'])->middleware('auth')->name('logout');
```

## 🔐 Процесс входа

### 1. Открытие страницы входа
```
GET /login
```

### 2. Отправка формы входа
```
POST /login
Body: email=test@test.com&password=password&_token={CSRF_TOKEN}
```

### 3. Обработка
- Валидация email и пароля
- Попытка аутентификации через guard 'web'
- При успехе: перенаправление на главную (/)
- При неудаче: возврат на страницу входа с ошибками

### 4. Создание сессии
- После успешного входа создается сессия
- Токен сессии сохраняется в cookie
- Пользователь может осуществлять запросы с авторизацией

## 🛡️ Безопасность

### CSRF Protection
- Каждая форма входа защищена CSRF токеном
- Токен генерируется автоматически через `@csrf` Blade директиву
- Проверка токена производится middleware `VerifyCsrfToken`

### Пароли
- Пароли хранятся с использованием Bcrypt хеширования
- Пароли никогда не логируются
- Пароли передаются через HTTPS

### Сессии
- Сессии шифруются
- Используется флаг `httponly` для cookie
- Автоматическое истечение сессии через 120 минут

## 🔄 Переменные окружения

```env
# Сессия
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

## 📝 Middleware

### RedirectIfAuthenticated
- **Файл:** `app/Http/Middleware/RedirectIfAuthenticated.php`
- **Использование:** middleware 'guest'
- **Функция:** Если пользователь уже авторизован, перенаправляет на главную (/)

### Authenticate
- **Файл:** `app/Http/Middleware/Authenticate.php`
- **Использование:** middleware 'auth'
- **Функция:** Проверяет авторизацию, при необходимости перенаправляет на /login

## 🚀 Использование

### Для входа в систему

1. Перейти на http://localhost/login
2. Ввести email: `test@test.com`
3. Ввести пароль: `password`
4. Нажать кнопку "Вход"
5. При успехе перенаправитесь на главную страницу

### Для выхода из системы

1. Отправить POST запрос на `/logout`
2. Сессия будет разрушена
3. Cookie будет удалена
4. Редирект на главную страницу

## 📱 API Аутентификация (JWT)

Отдельная реализация JWT аутентификации для API доступна:

- **POST /api/login** - Получить JWT токен
- **GET /api/me** - Текущий пользователь
- **POST /api/refresh** - Обновить токен
- **POST /api/logout** - Выход

Подробнее: [JWT Documentation](JWT_DOCUMENTATION.md)

## ⚙️ Конфигурация

### config/auth.php
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
]
```

### app/Providers/RouteServiceProvider.php
```php
public const HOME = '/';  // Путь для редиректа после входа
```

## 🐛 Возможные проблемы

### "Page Expired" (419 ошибка)
- **Причина:** CSRF токен истек или невалиден
- **Решение:** Перезагрузить страницу и попробовать еще раз

### "Неверные учетные данные"
- **Причина:** Неправильный email или пароль
- **Решение:** Проверить учетные данные

### Редирект в цикл
- **Причина:** Проблема с middleware 'guest'
- **Решение:** Очистить кеш: `php artisan cache:clear`

## 🔗 Связанные файлы

- User Model: `app/Models/User.php`
- AuthController: `app/Http/Controllers/AuthController.php`
- Login View: `resources/views/auth/login.blade.php`
- Web Routes: `routes/web.php`
- Middleware: `app/Http/Middleware/`

## 📚 Дополнительные ресурсы

- [Laravel Authentication](https://laravel.com/docs/10.x/authentication)
- [Laravel Sessions](https://laravel.com/docs/10.x/session)
- [CSRF Protection](https://laravel.com/docs/10.x/csrf)
