---
name: laravel-backend
description: Специализированный навык для работы с Laravel 12 + PHP 8.3 бэкендом проекта fullstack_test. Используйте этот skill при работе с API, бизнес-логикой, моделями, сервисами, консольными командами и архитектурой бэкенда. Применяется для создания новых эндпоинтов, обработчиков (handlers), сервисов, миграций, команд и рефакторинга существующего кода. Проект работает на стеке Docker + Nginx + PHP-FPM.
---

# Laravel Backend Skill

Этот skill предоставляет руководство по работе с бэкендом проекта на Laravel 12 + PHP 8.3 с использованием специфической архитектуры на основе Handler-паттерна.

## Архитектурные принципы

Проект использует модульную архитектуру с разделением на слои:

- **Api** - REST API эндпоинты для клиентских приложений
- **Console** - консольные команды Artisan
- **Web** - веб-интерфейс (если требуется)
- **Shared** - общие компоненты (модели, сервисы, responses)

### Ключевые паттерны

1. **Command-Handler Pattern**: Каждое действие разделено на Command (DTO с данными) и Handler (бизнес-логика)
2. **Thin Controllers**: Контроллеры только принимают запросы и возвращают ответы
3. **Service Layer**: Переиспользуемая логика вынесена в сервисы
4. **Standardized Responses**: Все API ответы через классы Response

## Структура проекта

```
php/app/
├── Api/
│   ├── Controllers/      # API контроллеры
│   ├── Handlers/         # Обработчики бизнес-логики API
│   ├── Queries/          # Query объекты для выборки данных
│   ├── Responses/        # Специфичные ответы API
│   └── routes.php        # API маршруты
├── Console/
│   ├── Commands/         # Artisan команды
│   ├── Handlers/         # Обработчики консольных команд
│   └── console.php       # Регистрация команд
├── Shared/
│   ├── Models/           # Eloquent модели
│   ├── Services/         # Переиспользуемые сервисы
│   ├── Responses/        # Базовые классы ответов
│   └── Handlers/         # Общие обработчики
└── Providers/            # Service Providers
```

## Рабочие процессы

### 1. Создание нового API эндпоинта

Для добавления нового API эндпоинта следуйте этой последовательности:

**Шаг 1: Создайте Command класс**

```php
// php/app/Api/Handlers/[Feature]/[Action]Command.php
<?php

namespace App\Api\Handlers\[Feature];

use Spatie\LaravelData\Data;

class [Action]Command extends Data
{
    public function __construct(
        public string $field1,
        public ?string $field2 = null,
    ) {}
}
```

**Шаг 2: Создайте Handler класс**

```php
// php/app/Api/Handlers/[Feature]/[Action].php
<?php

namespace App\Api\Handlers\[Feature];

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class [Action]
{
    protected function validate([Action]Command $command): void
    {
        $validator = Validator::make($command->toArray(), [
            'field1' => ['required', 'string'],
            'field2' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function handle([Action]Command $command): mixed
    {
        $this->validate($command);
        
        // Бизнес-логика здесь
        
        return $result;
    }
}
```

**Шаг 3: Создайте или обновите Controller**

```php
// php/app/Api/Controllers/[Feature]Controller.php
<?php

namespace App\Api\Controllers;

use App\Api\Handlers\[Feature]\[Action];
use App\Api\Handlers\[Feature]\[Action]Command;
use App\Shared\Responses\Api\SuccessResponse;
use Illuminate\Contracts\Support\Responsable;

class [Feature]Controller extends BaseClientController
{
    public function [action]([Action]Command $command, [Action] $handler): Responsable
    {
        $result = $handler->handle($command);

        return new SuccessResponse($result);
    }
}
```

**Шаг 4: Зарегистрируйте маршрут**

```php
// php/app/Api/routes.php
Route::post('/[route]', [[Feature]Controller::class, '[action]']);
```

### 2. Работа с моделями

Все модели находятся в `php/app/Shared/Models/`.

**Создание новой модели:**

```php
<?php

namespace App\Shared\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 */
class [ModelName] extends Model
{
    protected $fillable = [
        'name',
        // другие поля
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
```

**Важно:** Всегда добавляйте PHPDoc блок с `@property` для всех полей модели.

### 3. Создание сервиса

Сервисы размещаются в `php/app/Shared/Services/[ServiceName]/`.

**Структура сервиса:**

```
Services/
└── [ServiceName]/
    ├── [ServiceName].php           # Основной класс сервиса
    ├── Contracts/                  # Интерфейсы (если нужны)
    ├── [ServiceName]Strategy.php   # Стратегии (если нужны)
    └── README.MD                   # Документация использования
```

**Пример сервиса:** См. `references/filesystem-service.md` для детального примера.

### 4. Создание консольной команды

**Шаг 1: Создайте Command класс**

```php
// php/app/Console/Handlers/[Action]Command.php
<?php

namespace App\Console\Handlers;

use Spatie\LaravelData\Data;

class [Action]Command extends Data
{
    public function __construct(
        public string $parameter,
    ) {}
}
```

**Шаг 2: Создайте Handler**

```php
// php/app/Console/Handlers/[Action].php
<?php

namespace App\Console\Handlers;

class [Action]
{
    public function handle([Action]Command $command): void
    {
        // Логика команды
    }
}
```

**Шаг 3: Создайте Artisan команду**

**ВАЖНО: Именование CLI команд**

Имя команды (`signature`) должно следовать формату: `x:{model|source}:{action}`

Где:
- `x` - префикс приложения (может быть `app`, `user`, `admin` и т.д.)
- `{model|source}` - модель или источник данных (например, `user`, `post`, `export`)
- `{action}` - действие (например, `create`, `update`, `delete`, `export`)

**Примеры правильных имен:**
- `app:user:create` - создание пользователя
- `app:post:publish` - публикация поста
- `app:export:users` - экспорт пользователей
- `admin:cache:clear` - очистка кэша (админ)

```php
// php/app/Console/Commands/[Action]Command.php
<?php

namespace App\Console\Commands;

use App\Console\Handlers\[Action];
use App\Console\Handlers\[Action]Command as HandlerCommand;
use Illuminate\Console\Command;

class [Action]Command extends Command
{
    // ВАЖНО: Формат имени - x:{model|source}:{action}
    protected $signature = 'app:[model]:[action] {parameter}';
    protected $description = 'Описание команды';

    public function handle([Action] $handler): int
    {
        $command = new HandlerCommand(
            parameter: $this->argument('parameter'),
        );

        $handler->handle($command);

        return self::SUCCESS;
    }
}
```

**Примеры из проекта:**

```php
// Создание пользователя
protected $signature = 'app:user:create {email} {name} {password}';

// Экспорт данных
protected $signature = 'app:export:users {--format=csv}';

// Очистка кэша
protected $signature = 'app:cache:clear {--tags=*}';
```

### 5. Работа с миграциями

Создание миграции:

```bash
./artisan make:migration create_[table_name]_table
```

**Структура миграции:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('[table_name]', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('[table_name]');
    }
};
```

### 6. Работа с аутентификацией

Проект использует **Laravel Sanctum** для API аутентификации.

**Создание токена:**

```php
$token = $user->createToken('tokenName')->plainTextToken;
```

**Защита маршрутов:**

```php
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Защищенные маршруты
});
```

**Получение текущего пользователя:**

```php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
```

## Стандарты кодирования PHP 8.3

### 1. Именование

- **Controllers**: `[Feature]Controller.php` (например, `UserController.php`)
- **Handlers**: `[Action].php` (например, `UpdateUser.php`)
- **Commands**: `[Action]Command.php` (например, `UpdateUserCommand.php`)
- **Models**: `[ModelName].php` в единственном числе (например, `User.php`)
- **Services**: `[ServiceName].php` (например, `TokenGenerator.php`)
- **CLI Commands**: `app:[model]:[action]` (например, `app:user:create`)

### 2. PHP 8.3 Возможности

**Используйте современные возможности PHP 8.3:**

#### Constructor Property Promotion

```php
// ✅ Хорошо - PHP 8.3
class UpdateUserCommand extends Data
{
    public function __construct(
        public string $name,
        public ?string $email = null,
        public readonly int $id = 0,
    ) {}
}

// ❌ Плохо - старый стиль
class UpdateUserCommand extends Data
{
    public string $name;
    public ?string $email;
    
    public function __construct(string $name, ?string $email = null)
    {
        $this->name = $name;
        $this->email = $email;
    }
}
```

#### Typed Properties

```php
// ✅ Всегда указывайте типы свойств
class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email'];
}
```

#### Union Types

```php
// ✅ Используйте union types где нужно
public function handle(string|int $id): User|null
{
    return User::find($id);
}
```

#### Null Safe Operator

```php
// ✅ Используйте ?-> для безопасного доступа
$userName = $user?->profile?->name;

// Вместо
$userName = $user !== null && $user->profile !== null 
    ? $user->profile->name 
    : null;
```

#### Match Expression

```php
// ✅ Используйте match вместо switch
$status = match($code) {
    200 => 'success',
    404 => 'not_found',
    500 => 'error',
    default => 'unknown',
};
```

#### Named Arguments

```php
// ✅ Используйте именованные аргументы для ясности
$command = new UpdateUserCommand(
    name: 'John Doe',
    email: 'john@example.com',
);

// Особенно полезно с необязательными параметрами
$user = User::create(
    name: 'John',
    email: 'john@example.com',
);
```

#### Attributes (Annotations)

```php
// ✅ Используйте атрибуты вместо PHPDoc где возможно
#[Route('/api/users', methods: ['GET'])]
public function index(): Response
{
    // ...
}
```

### 3. Type Hints - ОБЯЗАТЕЛЬНО

**Всегда указывайте типы для:**
- Параметров методов
- Возвращаемых значений
- Свойств классов

```php
// ✅ Хорошо - полная типизация
public function handle(UpdateUserCommand $command, User $user): array
{
    return ['id' => $user->id];
}

// ❌ Плохо - без типов
public function handle($command, $user)
{
    return ['id' => $user->id];
}
```

**Используйте строгие типы:**

```php
<?php

declare(strict_types=1);

namespace App\Api\Handlers;
```

### 4. Валидация

Всегда выполняйте валидацию в Handler через отдельный метод `validate()`:

```php
protected function validate(UpdateUserCommand $command): void
{
    $validator = Validator::make($command->toArray(), [
        'field' => ['required', 'string', 'max:255'],
    ]);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }
}
```

### 5. Responses

Используйте стандартизированные классы ответов:

- `SuccessResponse` - для успешных ответов
- `ErrorResponse` - для ошибок
- Кастомные Response классы для специфичных случаев

```php
// ✅ Всегда указывайте тип возврата
public function update(UpdateUserCommand $command, UpdateUser $handler): Responsable
{
    $result = $handler->handle($command, Auth::user());
    return new SuccessResponse($result);
}
```

### 6. Dependency Injection

Используйте DI в конструкторах и методах:

```php
// ✅ DI в методе
public function handle(
    UpdateUserCommand $command,
    SomeService $service
): array {
    return $service->process($command);
}

// ✅ DI в конструкторе с property promotion
public function __construct(
    private readonly SomeService $service,
) {}
```

### 7. Readonly Properties

Используйте `readonly` для неизменяемых свойств:

```php
class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly CacheService $cache,
    ) {}
}
```

### 8. Enums (PHP 8.1+)

Используйте enums для фиксированных наборов значений:

```php
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
}

// Использование
$user->status = UserStatus::ACTIVE;
```

### 9. Array Shapes (PHPDoc)

Документируйте структуру массивов:

```php
/**
 * @return array{id: int, name: string, email: string}
 */
public function toArray(): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
    ];
}
```

### 10. Форматирование кода

Следуйте **PSR-12** стандарту:

```php
<?php

declare(strict_types=1);

namespace App\Api\Handlers\User;

use App\Shared\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUser
{
    protected function validate(UpdateUserCommand $command): void
    {
        $validator = Validator::make($command->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function handle(UpdateUserCommand $command, User $user): array
    {
        $this->validate($command);

        $user->name = $command->name;
        
        if ($command->email !== null) {
            $user->email = $command->email;
        }

        $user->save();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }
}
```

**Ключевые правила PSR-12:**
- Отступы: 4 пробела (не табы)
- Открывающая фигурная скобка на новой строке для классов и методов
- Одна пустая строка после namespace
- Одна пустая строка после use блока
- Максимум одно выражение на строку

## Технологический стек

- **PHP**: 8.3
- **Laravel**: 12.0
- **Nginx + PHP-FPM**: веб-сервер и обработка PHP (в Docker)
- **Laravel Sanctum**: аутентификация API
- **Spatie Laravel Data**: для DTO
- **PHPStan**: статический анализ (уровень 5)
- **Pest**: тестирование
- **PostgreSQL**: база данных
- **Redis**: кэш и очереди

## Полезные команды

**Важно:** Все команды PHP выполняются через sh-скрипты в корне проекта:
- `./artisan` - для команд Artisan
- `./composer` - для Composer
- `./phpstan` - для статического анализа
- `./psql` - для работы с PostgreSQL

```bash
# Docker контейнеры
make up              # Запуск приложения
make down            # Остановка
make build           # Пересборка
make restart         # Перезапуск

# Composer
./composer install   # Установка зависимостей
./composer update    # Обновление зависимостей
./composer require [package]  # Добавление пакета

# Artisan команды
./artisan migrate              # Запуск миграций
./artisan make:migration [name]  # Создание миграции
./artisan make:model [name]    # Создание модели
./artisan test                 # Запуск тестов
./artisan pail                 # Просмотр логов
./artisan route:list           # Список маршрутов
./artisan tinker               # REPL консоль

# Статический анализ
./phpstan analyse              # Запуск PHPStan

# База данных
./psql                         # Подключение к PostgreSQL
```

## Дополнительные ресурсы

- **Shell Scripts**: См. `references/shell-scripts.md` для работы с ./artisan, ./composer, ./phpstan, ./psql
- **PHP 8.3 Standards**: См. `references/php83-standards.md` для полного руководства по стандартам PHP 8.3
- **Architecture Overview**: См. `references/architecture-overview.md` для детального обзора архитектуры
- **Filesystem Service**: См. `references/filesystem-service.md` для работы с файлами
- **Response Classes**: См. `references/response-classes.md` для всех типов ответов
- **Testing Guide**: См. `references/testing-guide.md` для написания тестов

## Примеры

### Пример 1: Создание эндпоинта для обновления профиля

См. существующую реализацию:
- Command: `php/app/Api/Handlers/User/UpdateUserCommand.php`
- Handler: `php/app/Api/Handlers/User/UpdateUser.php`
- Controller: `php/app/Api/Controllers/UserController.php`

### Пример 2: Аутентификация

См. существующую реализацию:
- Command: `php/app/Api/Handlers/Signin/SigninCommand.php`
- Handler: `php/app/Api/Handlers/Signin/Signin.php`
- Controller: `php/app/Api/Controllers/AuthController.php`

## Troubleshooting

### Проблема: ValidationException не отображается правильно

**Решение:** Убедитесь, что используете `Illuminate\Validation\ValidationException` и выбрасываете его после проверки `$validator->fails()`.

### Проблема: Токен Sanctum не работает

**Решение:** Проверьте, что:
1. Middleware `auth:sanctum` добавлен к маршруту
2. Токен передается в заголовке `Authorization: Bearer {token}`
3. Модель User использует трейт `HasApiTokens`

### Проблема: Изменения не применяются в Docker

**Решение:** 
1. Проверьте, что volume правильно примонтирован в `docker-compose.yml`
2. Перезапустите PHP-FPM: `make restart`
3. Очистите кэш Laravel: `./artisan cache:clear` и `./artisan config:clear`

### Проблема: Ошибка "Permission denied" при выполнении ./artisan или ./composer

**Решение:**
```bash
chmod +x ./artisan
chmod +x ./composer
chmod +x ./phpstan
chmod +x ./psql
```

### Проблема: PHPStan находит ошибки типов

**Решение:**
1. Всегда указывайте типы для параметров и возвращаемых значений
2. Используйте PHPDoc для сложных типов (массивы, union types)
3. Добавьте `@var` аннотации где PHPStan не может вывести тип
4. Проверьте уровень PHPStan в `phpstan.neon` (текущий: 5)
