# PHP 8.3 Стандарты кодирования

Полное руководство по стандартам написания кода на PHP 8.3 для проекта.

## Общие принципы

1. **Строгая типизация** - всегда используйте `declare(strict_types=1)`
2. **PSR-12** - следуйте стандарту форматирования кода
3. **Современный PHP** - используйте возможности PHP 8.3
4. **Типобезопасность** - указывайте типы везде
5. **Читаемость** - код должен быть понятен без комментариев

## Структура файла

```php
<?php

declare(strict_types=1);

namespace App\Api\Handlers\User;

use App\Shared\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUser
{
    // Код класса
}
```

**Порядок элементов:**
1. `<?php` тег
2. `declare(strict_types=1);`
3. `namespace`
4. `use` выражения (группировка по типу)
5. Код класса

## PHP 8.3 Возможности

### 1. Constructor Property Promotion

**✅ Используйте:**

```php
class UpdateUserCommand extends Data
{
    public function __construct(
        public string $name,
        public ?string $email = null,
        public readonly int $userId = 0,
    ) {}
}
```

**❌ Не используйте старый стиль:**

```php
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

### 2. Readonly Properties

Используйте `readonly` для неизменяемых свойств:

```php
class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly CacheService $cache,
    ) {}
    
    // $repository и $cache нельзя изменить после создания
}
```

### 3. Typed Properties

**Всегда указывайте типы свойств:**

```php
class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email'];
    protected array $hidden = ['password'];
    protected bool $timestamps = true;
}
```

### 4. Union Types

```php
// Параметр может быть string или int
public function find(string|int $id): User|null
{
    return User::find($id);
}

// Возвращаемое значение может быть array или Collection
public function getUsers(): array|Collection
{
    return User::all();
}
```

### 5. Null Safe Operator (?->)

```php
// ✅ Безопасный доступ к свойствам
$userName = $user?->profile?->name;
$firstPost = $user?->posts?->first()?->title;

// Вместо
$userName = null;
if ($user !== null && $user->profile !== null) {
    $userName = $user->profile->name;
}
```

### 6. Match Expression

**✅ Используйте match:**

```php
$message = match($status) {
    200 => 'Success',
    404 => 'Not Found',
    500 => 'Server Error',
    default => 'Unknown Status',
};

// Более сложный пример
$result = match(true) {
    $age < 18 => 'minor',
    $age >= 18 && $age < 65 => 'adult',
    $age >= 65 => 'senior',
};
```

**❌ Вместо switch:**

```php
switch($status) {
    case 200:
        $message = 'Success';
        break;
    case 404:
        $message = 'Not Found';
        break;
    default:
        $message = 'Unknown';
}
```

### 7. Named Arguments

**✅ Используйте для ясности:**

```php
// Создание объектов
$command = new UpdateUserCommand(
    name: 'John Doe',
    email: 'john@example.com',
    userId: 123,
);

// Вызов функций
$user = User::create(
    name: 'John',
    email: 'john@example.com',
    password: Hash::make('password'),
);

// Особенно полезно при пропуске необязательных параметров
$result = someFunction(
    required: 'value',
    optional3: 'value3',  // Пропускаем optional1 и optional2
);
```

### 8. Enums (PHP 8.1+)

**Используйте enums для фиксированных значений:**

```php
enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case BANNED = 'banned';
    case PENDING = 'pending';
    
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Активный',
            self::INACTIVE => 'Неактивный',
            self::BANNED => 'Заблокирован',
            self::PENDING => 'Ожидает',
        };
    }
    
    public function canLogin(): bool
    {
        return $this === self::ACTIVE;
    }
}

// Использование
$user->status = UserStatus::ACTIVE;

if ($user->status->canLogin()) {
    // Разрешить вход
}
```

**Backed Enums (с значениями):**

```php
enum HttpStatus: int
{
    case OK = 200;
    case NOT_FOUND = 404;
    case SERVER_ERROR = 500;
}

// Получение значения
$code = HttpStatus::OK->value; // 200

// Создание из значения
$status = HttpStatus::from(200); // HttpStatus::OK
```

### 9. Array Unpacking

```php
// Объединение массивов
$array1 = [1, 2, 3];
$array2 = [4, 5, 6];
$combined = [...$array1, ...$array2]; // [1, 2, 3, 4, 5, 6]

// С ассоциативными массивами
$defaults = ['color' => 'blue', 'size' => 'medium'];
$custom = ['color' => 'red'];
$config = [...$defaults, ...$custom]; // ['color' => 'red', 'size' => 'medium']
```

### 10. First-class Callable Syntax

```php
// ✅ Новый синтаксис
$callback = strlen(...);
$result = array_map($callback, $strings);

// Методы
$callback = $object->method(...);
$callback = SomeClass::staticMethod(...);

// Вместо
$callback = fn($str) => strlen($str);
```

## Типизация

### Параметры и возвращаемые значения

**✅ Всегда указывайте типы:**

```php
public function handle(UpdateUserCommand $command, User $user): array
{
    return ['id' => $user->id];
}

public function find(int $id): ?User
{
    return User::find($id);
}

public function getAll(): Collection
{
    return User::all();
}
```

### Nullable Types

```php
// Параметр может быть null
public function setName(?string $name): void
{
    $this->name = $name;
}

// Возвращаемое значение может быть null
public function findUser(int $id): ?User
{
    return User::find($id);
}
```

### Mixed Type

Используйте `mixed` только когда действительно нужен любой тип:

```php
public function process(mixed $data): mixed
{
    // Обработка различных типов данных
    return $result;
}
```

### Void Type

```php
public function delete(User $user): void
{
    $user->delete();
    // Ничего не возвращаем
}
```

### Never Type (PHP 8.1+)

Для функций, которые никогда не возвращают управление:

```php
public function fail(string $message): never
{
    throw new Exception($message);
}

public function redirect(string $url): never
{
    header("Location: $url");
    exit;
}
```

## PHPDoc Аннотации

Используйте PHPDoc для документирования сложных типов:

### Array Shapes

```php
/**
 * @return array{id: int, name: string, email: string, created_at: string}
 */
public function toArray(): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'created_at' => $this->created_at->toIso8601String(),
    ];
}
```

### Generics

```php
/**
 * @param array<int, User> $users
 * @return Collection<int, User>
 */
public function processUsers(array $users): Collection
{
    return collect($users);
}
```

### Property Types

```php
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property UserStatus $status
 * @property \Carbon\Carbon $created_at
 */
class User extends Model
{
    // ...
}
```

## PSR-12 Форматирование

### Отступы и пробелы

```php
// ✅ 4 пробела для отступов
class Example
{
    public function method(): void
    {
        if ($condition) {
            // Код
        }
    }
}

// ❌ Не используйте табы
```

### Фигурные скобки

```php
// ✅ Открывающая скобка на новой строке для классов и методов
class Example
{
    public function method(): void
    {
        // Код
    }
}

// ✅ Открывающая скобка на той же строке для control structures
if ($condition) {
    // Код
} else {
    // Код
}
```

### Длина строки

```php
// ✅ Рекомендуется до 120 символов
// Переносите длинные строки

// Параметры метода
public function longMethodName(
    string $parameter1,
    int $parameter2,
    array $parameter3
): array {
    // Код
}

// Массивы
$array = [
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => 'value3',
];

// Цепочки методов
$result = SomeClass::query()
    ->where('status', 'active')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();
```

### Пустые строки

```php
<?php

declare(strict_types=1);

namespace App\Api\Handlers;  // Одна пустая строка после declare

use App\Shared\Models\User;
use Illuminate\Support\Facades\Validator;  // Одна пустая строка после use блока

class UpdateUser  // Одна пустая строка перед классом
{
    protected function validate(UpdateUserCommand $command): void
    {
        // Код
    }  // Одна пустая строка между методами

    public function handle(UpdateUserCommand $command): array
    {
        // Код
    }
}
```

## Именование

### Классы

```php
// ✅ PascalCase
class UpdateUser {}
class UserController {}
class UpdateUserCommand {}
```

### Методы и функции

```php
// ✅ camelCase
public function handleRequest(): void {}
public function getUserById(int $id): ?User {}
```

### Переменные

```php
// ✅ camelCase
$userName = 'John';
$isActive = true;
$userList = [];
```

### Константы

```php
// ✅ UPPER_SNAKE_CASE
const MAX_USERS = 100;
const DEFAULT_STATUS = 'active';
```

### Приватные свойства

```php
// ✅ camelCase (без подчеркивания)
private string $userName;
private bool $isActive;
```

## Лучшие практики

### 1. Ранний возврат

**✅ Используйте:**

```php
public function process(User $user): array
{
    if (!$user->isActive()) {
        return [];
    }
    
    if ($user->isBanned()) {
        throw new Exception('User is banned');
    }
    
    return $this->doProcess($user);
}
```

**❌ Избегайте глубокой вложенности:**

```php
public function process(User $user): array
{
    if ($user->isActive()) {
        if (!$user->isBanned()) {
            return $this->doProcess($user);
        } else {
            throw new Exception('User is banned');
        }
    } else {
        return [];
    }
}
```

### 2. Избегайте else после return

```php
// ✅ Хорошо
public function getStatus(User $user): string
{
    if ($user->isActive()) {
        return 'active';
    }
    
    if ($user->isBanned()) {
        return 'banned';
    }
    
    return 'inactive';
}

// ❌ Плохо
public function getStatus(User $user): string
{
    if ($user->isActive()) {
        return 'active';
    } else {
        if ($user->isBanned()) {
            return 'banned';
        } else {
            return 'inactive';
        }
    }
}
```

### 3. Используйте коллекции вместо массивов

```php
// ✅ Хорошо
public function getActiveUsers(): Collection
{
    return User::where('is_active', true)->get();
}

$names = $users->pluck('name');
$filtered = $users->filter(fn($user) => $user->age > 18);
```

### 4. Избегайте магических чисел

```php
// ✅ Хорошо
const MAX_LOGIN_ATTEMPTS = 3;
const SESSION_TIMEOUT = 3600;

if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
    // Блокировка
}

// ❌ Плохо
if ($attempts >= 3) {
    // Блокировка
}
```

### 5. Используйте строгое сравнение

```php
// ✅ Всегда используйте === и !==
if ($value === null) {}
if ($count === 0) {}
if ($status !== 'active') {}

// ❌ Избегайте == и !=
if ($value == null) {}  // Может дать неожиданные результаты
```

## Проверка кода

### PHPStan

Запуск статического анализа:

```bash
./phpstan analyse
```

Проект использует уровень 5. Исправляйте все найденные ошибки.

### Общие ошибки PHPStan

```php
// ❌ PHPStan ошибка: Parameter has no type
public function handle($command) {}

// ✅ Исправлено
public function handle(UpdateUserCommand $command): void {}

// ❌ PHPStan ошибка: Method has no return type
public function getUser(int $id) {}

// ✅ Исправлено
public function getUser(int $id): ?User {}
```

## Чеклист

Перед коммитом кода проверьте:

- [ ] `declare(strict_types=1)` в начале файла
- [ ] Все параметры имеют типы
- [ ] Все методы имеют return type
- [ ] Используется constructor property promotion где возможно
- [ ] Используются readonly свойства где возможно
- [ ] PHPDoc для сложных типов (array shapes, generics)
- [ ] PSR-12 форматирование (4 пробела, скобки, пустые строки)
- [ ] Нет магических чисел
- [ ] Используется строгое сравнение (===)
- [ ] PHPStan не находит ошибок

## Примеры

### Полный пример класса

```php
<?php

declare(strict_types=1);

namespace App\Api\Handlers\User;

use App\Shared\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUser
{
    private const MIN_PASSWORD_LENGTH = 8;

    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    /**
     * @return array{id: int, name: string, email: string}
     */
    public function handle(UpdateUserCommand $command, User $user): array
    {
        $this->validate($command);

        $user->name = $command->name;

        if ($command->email !== null) {
            $user->email = $command->email;
        }

        if ($command->password !== null) {
            $user->password = Hash::make($command->password);
        }

        $user->save();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    protected function validate(UpdateUserCommand $command): void
    {
        $validator = Validator::make($command->toArray(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:' . self::MIN_PASSWORD_LENGTH],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
```

## Заключение

Следование этим стандартам обеспечивает:
- **Типобезопасность** - меньше ошибок во время выполнения
- **Читаемость** - код легко понять
- **Поддерживаемость** - легко вносить изменения
- **Совместимость** - работает с PHPStan и другими инструментами
- **Современность** - используются возможности PHP 8.3
