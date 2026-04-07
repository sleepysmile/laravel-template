# Architecture Overview

Обзор архитектуры бэкенда проекта fullstack_test.

## Архитектурные принципы

### 1. Command-Handler Pattern

Каждое действие разделено на два класса:

**Command (DTO)** - содержит только данные:
```php
class UpdateUserCommand extends Data
{
    public function __construct(
        public string $name,
        public ?string $email = null,
    ) {}
}
```

**Handler** - содержит бизнес-логику:
```php
class UpdateUser
{
    public function handle(UpdateUserCommand $command, User $user): array
    {
        // Валидация
        // Бизнес-логика
        // Возврат результата
    }
}
```

**Преимущества:**
- Четкое разделение данных и логики
- Легко тестировать
- Переиспользуемые handlers
- Типобезопасность

### 2. Thin Controllers

Контроллеры минимальны и только координируют:

```php
class UserController extends BaseClientController
{
    public function update(UpdateUserCommand $command, UpdateUser $handler)
    {
        $result = $handler->handle($command, Auth::user());
        return new SuccessResponse($result);
    }
}
```

**Контроллер НЕ должен:**
- Содержать бизнес-логику
- Работать напрямую с моделями (кроме простейших случаев)
- Содержать валидацию (валидация в Handler)

**Контроллер ДОЛЖЕН:**
- Принимать Command и Handler через DI
- Вызывать handler
- Возвращать Response

### 3. Service Layer

Переиспользуемая логика вынесена в сервисы:

```php
// Filesystem Service
$fs = new ModelFilesystem($adapter);
$path = $fs->store($user, 'avatar', $file);

// Token Generator Service
$generator = new TokenGenerator();
$token = $generator->token();
```

**Когда создавать сервис:**
- Логика используется в нескольких местах
- Сложная операция (работа с файлами, API, и т.д.)
- Нужна изоляция для тестирования
- Требуется конфигурация или состояние

### 4. Modular Structure

Код организован по модулям:

```
app/
├── Api/              # REST API для клиентов
│   ├── Controllers/
│   ├── Handlers/
│   ├── Queries/
│   └── Responses/
├── Console/          # CLI команды
│   ├── Commands/
│   └── Handlers/
├── Web/              # Web интерфейс (если нужен)
│   └── routes.php
└── Shared/           # Общие компоненты
    ├── Models/
    ├── Services/
    ├── Responses/
    └── Handlers/
```

## Поток данных

### API Request Flow

```
1. Request → Route → Controller
   ↓
2. Controller → Command (auto-binding)
   ↓
3. Controller → Handler (DI)
   ↓
4. Handler → validate(Command)
   ↓
5. Handler → Business Logic
   ↓
6. Handler → return result
   ↓
7. Controller → Response
   ↓
8. Response → JSON
```

### Пример полного потока

```php
// 1. Route
Route::post('/user', [UserController::class, 'update']);

// 2. Controller
class UserController extends BaseClientController
{
    public function update(UpdateUserCommand $command, UpdateUser $handler)
    {
        // 3. Handler вызов
        $result = $handler->handle($command, Auth::user());
        
        // 4. Response
        return new SuccessResponse($result);
    }
}

// 5. Handler
class UpdateUser
{
    protected function validate(UpdateUserCommand $command): void
    {
        // Валидация
    }

    public function handle(UpdateUserCommand $command, User $user): array
    {
        $this->validate($command);
        
        // Бизнес-логика
        $user->name = $command->name;
        $user->save();
        
        return ['id' => $user->id, 'name' => $user->name];
    }
}
```

## Dependency Injection

Laravel автоматически резолвит зависимости:

```php
// В контроллере
public function method(
    SomeCommand $command,        // Auto-binding из request
    SomeHandler $handler,        // DI из контейнера
    SomeService $service         // DI из контейнера
) {
    // Все параметры автоматически инжектятся
}

// В handler
public function handle(
    SomeCommand $command,
    User $user,                  // Передается из контроллера
    AnotherService $service      // DI из контейнера
) {
    // ...
}
```

## Валидация

Валидация всегда в Handler, не в Controller:

```php
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
        // ...
    }
}
```

**Почему в Handler:**
- Handler можно переиспользовать (например, из Console)
- Легче тестировать
- Валидация - часть бизнес-логики

## Аутентификация

Используется Laravel Sanctum:

```php
// Создание токена
$token = $user->createToken('tokenName')->plainTextToken;

// Защита маршрута
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/user', [UserController::class, 'update']);
});

// Получение пользователя
$user = Auth::user();
```

## Responses

Стандартизированные ответы:

```php
// Успех
return new SuccessResponse(['id' => 1, 'name' => 'John']);

// Ошибка (автоматически через исключения)
throw new ValidationException($validator);  // 422
throw new LogicException('Error message');  // 400
throw new AuthenticationException();        // 401
```

## Модели

Eloquent модели в `Shared/Models/`:

```php
/**
 * @property int $id
 * @property string $name
 */
class User extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['name', 'email'];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
}
```

**Важно:**
- Всегда добавляйте PHPDoc с @property
- Используйте $fillable для массового заполнения
- Используйте casts для типизации
- Отношения определяйте как методы

## Queries

Для сложных запросов создавайте Query классы:

```php
class FindActiveUsers
{
    public function execute(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

// Использование
class SomeHandler
{
    public function handle(FindActiveUsers $query): array
    {
        $users = $query->execute();
        return $users->toArray();
    }
}
```

## Тестирование

### Feature тесты

Тестируют полный поток (HTTP → Response):

```php
test('user can update profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    
    $response = postJson('/api/user', ['name' => 'New Name']);
    
    $response->assertStatus(200);
    expect($user->fresh()->name)->toBe('New Name');
});
```

### Unit тесты

Тестируют отдельные компоненты:

```php
test('handler updates user', function () {
    $user = User::factory()->create();
    $command = new UpdateUserCommand(name: 'New Name');
    $handler = new UpdateUser();
    
    $result = $handler->handle($command, $user);
    
    expect($result['name'])->toBe('New Name');
});
```

## Инфраструктура

### Docker + Nginx + PHP-FPM

Проект работает на стеке:
- **Docker** - контейнеризация
- **Nginx** - веб-сервер
- **PHP-FPM 8.3** - обработка PHP
- **PostgreSQL** - база данных
- **Redis** - кэш и очереди

```bash
# Управление контейнерами
make up       # Запуск
make down     # Остановка
make restart  # Перезапуск
make build    # Пересборка
```

### Выполнение команд

Все PHP команды выполняются через sh-скрипты:

```bash
./artisan [command]   # Artisan команды
./composer [command]  # Composer команды
./phpstan analyse     # Статический анализ
./psql               # PostgreSQL консоль
```

Эти скрипты автоматически выполняют команды внутри Docker контейнера.

## Статический анализ

PHPStan уровень 5:

```bash
./phpstan analyse
```

**PHP 8.3 - Всегда указывайте типы:**

```php
<?php

declare(strict_types=1);

namespace App\Api\Handlers;

// ✅ Полная типизация
public function method(string $param): array
{
    return [];
}

// ✅ Используйте современные возможности PHP 8.3
public function __construct(
    private readonly SomeService $service,
) {}

// ✅ Union types
public function find(string|int $id): User|null
{
    return User::find($id);
}

// ✅ Named arguments
$command = new UpdateUserCommand(
    name: 'John',
    email: 'john@example.com',
);
```

## Лучшие практики

1. **Один Handler - одна ответственность**
2. **Валидация в Handler, не в Controller**
3. **Используйте DI вместо фасадов где возможно**
4. **Всегда типизируйте параметры и возвращаемые значения**
5. **Пишите тесты для критичной логики**
6. **Используйте фабрики для тестовых данных**
7. **Документируйте сложную логику**
8. **Следуйте PSR-12 стандарту**

## Антипаттерны

❌ **Не делайте:**

```php
// Бизнес-логика в контроллере
public function update(Request $request)
{
    $user = Auth::user();
    $user->name = $request->name;
    $user->save();
    return response()->json($user);
}

// Прямые запросы в контроллере
public function index()
{
    $users = User::where('active', true)->get();
    return response()->json($users);
}
```

✅ **Делайте:**

```php
// Через Handler
public function update(UpdateUserCommand $command, UpdateUser $handler)
{
    $result = $handler->handle($command, Auth::user());
    return new SuccessResponse($result);
}

// Через Query
public function index(FindActiveUsers $query)
{
    $users = $query->execute();
    return new SuccessResponse($users);
}
```

## Заключение

Архитектура проекта обеспечивает:
- **Чистоту кода** - четкое разделение ответственности
- **Тестируемость** - легко писать unit и feature тесты
- **Масштабируемость** - легко добавлять новые функции
- **Поддерживаемость** - понятная структура и паттерны
- **Производительность** - Laravel Octane + оптимизации
