# Testing Guide

Руководство по написанию тестов для Laravel бэкенда с использованием Pest.

## Настройка тестирования

Проект использует **Pest 4.3** - современный фреймворк для тестирования PHP.

### Структура тестов

```
php/tests/
├── TestCase.php           # Базовый класс для тестов
├── Feature/               # Функциональные тесты (HTTP, команды)
│   ├── AuthTest.php
│   └── UserTest.php
├── Unit/                  # Юнит-тесты (сервисы, модели)
│   ├── ServiceTest.php
│   └── ModelTest.php
└── Factory/               # Кастомные фабрики
    └── UserFactory.php
```

## Запуск тестов

```bash
# Все тесты
./artisan test

# Конкретный файл
./artisan test tests/Feature/AuthTest.php

# Конкретный тест
./artisan test --filter test_user_can_signin

# С покрытием кода
./artisan test --coverage

# Параллельное выполнение
./artisan test --parallel
```

## Feature тесты (API эндпоинты)

### Базовая структура

```php
<?php

use App\Shared\Models\User;
use function Pest\Laravel\{postJson, getJson};

test('user can signin with valid credentials', function () {
    // Arrange - подготовка данных
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Act - выполнение действия
    $response = postJson('/api/signin', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    // Assert - проверка результата
    $response->assertStatus(200)
        ->assertJsonStructure(['token']);
});
```

### Тестирование аутентификации

```php
<?php

use App\Shared\Models\User;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\{postJson, actingAs};

test('authenticated user can update profile', function () {
    $user = User::factory()->create();
    
    // Аутентификация через Sanctum
    Sanctum::actingAs($user);
    
    $response = postJson('/api/user', [
        'name' => 'New Name',
    ]);
    
    $response->assertStatus(200);
    expect($user->fresh()->name)->toBe('New Name');
});

test('unauthenticated user cannot update profile', function () {
    $response = postJson('/api/user', [
        'name' => 'New Name',
    ]);
    
    $response->assertStatus(401);
});
```

### Тестирование валидации

```php
<?php

use function Pest\Laravel\postJson;

test('signin requires email', function () {
    $response = postJson('/api/signin', [
        'password' => 'password123',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('signin requires valid email format', function () {
    $response = postJson('/api/signin', [
        'email' => 'invalid-email',
        'password' => 'password123',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('signin requires password with minimum length', function () {
    $response = postJson('/api/signin', [
        'email' => 'test@example.com',
        'password' => 'ab',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
```

### Тестирование бизнес-логики

```php
<?php

use App\Shared\Models\User;
use function Pest\Laravel\postJson;

test('signin fails with incorrect password', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('correct-password'),
    ]);
    
    $response = postJson('/api/signin', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);
    
    $response->assertStatus(400)
        ->assertJson(['message' => 'неверный пароль']);
});

test('signin fails for non-existent user', function () {
    $response = postJson('/api/signin', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);
    
    $response->assertStatus(400)
        ->assertJson(['message' => 'такой пользователь не зарегестрирован']);
});
```

## Unit тесты

### Тестирование Handler классов

```php
<?php

use App\Api\Handlers\Signin\Signin;
use App\Api\Handlers\Signin\SigninCommand;
use App\Shared\Models\User;
use Illuminate\Validation\ValidationException;

test('signin handler returns token for valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    
    $handler = new Signin();
    $command = new SigninCommand(
        email: 'test@example.com',
        password: 'password123'
    );
    
    $token = $handler->handle($command);
    
    expect($token)->toBeString();
    expect(strlen($token))->toBeGreaterThan(0);
});

test('signin handler throws exception for invalid email', function () {
    $handler = new Signin();
    $command = new SigninCommand(
        email: 'invalid-email',
        password: 'password123'
    );
    
    $handler->handle($command);
})->throws(ValidationException::class);
```

### Тестирование сервисов

```php
<?php

use App\Shared\Services\Token\TokenGenerator;

test('token generator creates unique tokens', function () {
    $generator1 = new TokenGenerator();
    $generator2 = new TokenGenerator();
    
    $token1 = $generator1->token();
    $token2 = $generator2->token();
    
    expect($token1)->toBeString();
    expect($token1)->not->toBe($token2);
});

test('token generator returns same token on multiple calls', function () {
    $generator = new TokenGenerator();
    
    $token1 = $generator->token();
    $token2 = $generator->token();
    
    expect($token1)->toBe($token2);
});
```

### Тестирование Filesystem Service

```php
<?php

use App\Shared\Models\User;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake();
});

test('model filesystem stores file and updates model', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg');
    
    $adapter = new FilesystemAdapter(Storage::disk());
    $fs = new ModelFilesystem($adapter);
    
    $path = $fs->store(
        $user,
        'avatar',
        FileAdapter::fromUploadedFile($file)
    );
    
    expect($path)->toBeString();
    expect($user->fresh()->avatar)->toBe($path);
    Storage::disk()->assertExists($path);
});

test('model filesystem deletes old file when updating', function () {
    $user = User::factory()->create(['avatar' => 'old-path.jpg']);
    Storage::disk()->put('old-path.jpg', 'content');
    
    $file = UploadedFile::fake()->image('new-avatar.jpg');
    
    $adapter = new FilesystemAdapter(Storage::disk());
    $fs = new ModelFilesystem($adapter);
    
    $fs->store($user, 'avatar', FileAdapter::fromUploadedFile($file));
    
    Storage::disk()->assertMissing('old-path.jpg');
});
```

## Тестирование моделей

```php
<?php

use App\Shared\Models\User;

test('user model has fillable attributes', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
    
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
});

test('user password is hashed', function () {
    $user = User::factory()->create([
        'password' => 'plain-password',
    ]);
    
    expect($user->password)->not->toBe('plain-password');
    expect(Hash::check('plain-password', $user->password))->toBeTrue();
});

test('user can create api token', function () {
    $user = User::factory()->create();
    
    $token = $user->createToken('test-token')->plainTextToken;
    
    expect($token)->toBeString();
    expect($user->tokens()->count())->toBe(1);
});
```

## Тестирование консольных команд

```php
<?php

use App\Shared\Models\User;
use function Pest\Laravel\artisan;

test('create user command creates user', function () {
    artisan('app:create-user', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'password' => 'password123',
    ])
        ->expectsOutput('User created successfully')
        ->assertSuccessful();
    
    $user = User::where('email', 'test@example.com')->first();
    
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Test User');
});
```

## Использование фабрик

### Создание фабрики

```php
<?php

namespace Database\Factories;

use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ];
    }

    public function withAvatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'avatar' => 'avatars/' . fake()->uuid() . '.jpg',
        ]);
    }
}
```

### Использование фабрики в тестах

```php
<?php

use App\Shared\Models\User;

test('can create user with factory', function () {
    $user = User::factory()->create();
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toBeString();
});

test('can create user with custom attributes', function () {
    $user = User::factory()->create([
        'name' => 'Custom Name',
    ]);
    
    expect($user->name)->toBe('Custom Name');
});

test('can create user with avatar', function () {
    $user = User::factory()->withAvatar()->create();
    
    expect($user->avatar)->not->toBeNull();
});

test('can create multiple users', function () {
    $users = User::factory()->count(5)->create();
    
    expect($users)->toHaveCount(5);
});
```

## Pest функции и матчеры

### Основные функции

```php
// Определение теста
test('description', function () {
    // ...
});

// Определение теста с использованием it
it('does something', function () {
    // ...
});

// Группировка тестов
describe('User Authentication', function () {
    test('user can signin', function () {
        // ...
    });
    
    test('user can signout', function () {
        // ...
    });
});
```

### Хуки

```php
// Выполняется перед каждым тестом
beforeEach(function () {
    // Подготовка
});

// Выполняется после каждого теста
afterEach(function () {
    // Очистка
});

// Выполняется один раз перед всеми тестами
beforeAll(function () {
    // Глобальная подготовка
});

// Выполняется один раз после всех тестов
afterAll(function () {
    // Глобальная очистка
});
```

### Матчеры (Expectations)

```php
// Базовые проверки
expect($value)->toBe('expected');
expect($value)->toEqual(['key' => 'value']);
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeNull();
expect($value)->not->toBeNull();

// Типы
expect($value)->toBeString();
expect($value)->toBeInt();
expect($value)->toBeArray();
expect($value)->toBeInstanceOf(User::class);

// Коллекции
expect($array)->toHaveCount(5);
expect($array)->toContain('value');
expect($array)->toHaveKey('key');

// Числа
expect($number)->toBeGreaterThan(10);
expect($number)->toBeLessThan(100);
expect($number)->toBeBetween(10, 100);

// Строки
expect($string)->toStartWith('prefix');
expect($string)->toEndWith('suffix');
expect($string)->toContain('substring');
expect($string)->toMatch('/regex/');

// Исключения
expect(fn() => throw new Exception())->toThrow(Exception::class);
expect(fn() => throw new Exception('message'))->toThrow(Exception::class, 'message');
```

### Laravel специфичные ассерты

```php
use function Pest\Laravel\{get, post, put, delete, getJson, postJson};

// HTTP тесты
get('/url')->assertStatus(200);
get('/url')->assertOk();
get('/url')->assertRedirect('/other');
get('/url')->assertViewIs('view.name');
get('/url')->assertViewHas('key', 'value');

// JSON ассерты
getJson('/api/users')
    ->assertStatus(200)
    ->assertJson(['key' => 'value'])
    ->assertJsonStructure(['data' => ['id', 'name']])
    ->assertJsonCount(5, 'data')
    ->assertJsonPath('data.0.name', 'John');

// База данных
assertDatabaseHas('users', ['email' => 'test@example.com']);
assertDatabaseMissing('users', ['email' => 'deleted@example.com']);
assertDatabaseCount('users', 10);

// Модели
assertModelExists($user);
assertModelMissing($deletedUser);
```

## Лучшие практики

### 1. Используйте AAA паттерн

```php
test('example', function () {
    // Arrange - подготовка данных
    $user = User::factory()->create();
    
    // Act - выполнение действия
    $result = $user->doSomething();
    
    // Assert - проверка результата
    expect($result)->toBeTrue();
});
```

### 2. Один тест - одна проверка

```php
// Плохо
test('user operations', function () {
    $user = User::factory()->create();
    expect($user->name)->toBeString();
    expect($user->email)->toBeString();
    $user->delete();
    expect(User::count())->toBe(0);
});

// Хорошо
test('user has string name', function () {
    $user = User::factory()->create();
    expect($user->name)->toBeString();
});

test('user can be deleted', function () {
    $user = User::factory()->create();
    $user->delete();
    expect(User::count())->toBe(0);
});
```

### 3. Используйте описательные имена

```php
// Плохо
test('test1', function () { /* ... */ });

// Хорошо
test('user can signin with valid credentials', function () { /* ... */ });
```

### 4. Изолируйте тесты

```php
// Используйте фабрики вместо реальных данных
test('example', function () {
    $user = User::factory()->create(); // Хорошо
    // вместо
    // $user = User::find(1); // Плохо
});
```

### 5. Тестируйте граничные случаи

```php
test('handles empty input', function () { /* ... */ });
test('handles null input', function () { /* ... */ });
test('handles maximum length input', function () { /* ... */ });
```

## Покрытие кода

```bash
# Запуск с покрытием
./artisan test --coverage

# Минимальное покрытие
./artisan test --coverage --min=80
```

Настройка в `phpunit.xml`:

```xml
<coverage>
    <include>
        <directory suffix=".php">./app</directory>
    </include>
    <exclude>
        <directory>./app/Providers</directory>
    </exclude>
</coverage>
```

## Заключение

Хорошие тесты:
- **Быстрые** - выполняются за секунды
- **Изолированные** - не зависят друг от друга
- **Повторяемые** - дают одинаковый результат
- **Понятные** - легко читаются
- **Поддерживаемые** - легко обновляются
