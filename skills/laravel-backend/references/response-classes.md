# Response Classes

Стандартизированные классы для формирования HTTP ответов в API.

## Иерархия классов

```
Shared/Responses/
├── Api/
│   ├── BaseResponse.php      # Базовый класс для API ответов
│   ├── SuccessResponse.php   # Успешный ответ
│   └── ErrorResponse.php     # Ответ с ошибкой (если существует)
└── [Другие типы ответов]
```

## BaseResponse

Базовый класс для всех API ответов. Реализует `Illuminate\Contracts\Support\Responsable`.

**Структура:**

```php
<?php

namespace App\Shared\Responses\Api;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

abstract class BaseResponse implements Responsable
{
    protected mixed $data;
    protected int $status;
    protected array $headers;

    public function __construct(
        mixed $data,
        int $status = 200,
        array $headers = []
    ) {
        $this->data = $data;
        $this->status = $status;
        $this->headers = $headers;
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json($this->data, $this->status, $this->headers);
    }
}
```

## SuccessResponse

Используется для успешных API ответов.

**Использование:**

```php
use App\Shared\Responses\Api\SuccessResponse;

// Простой ответ с данными
return new SuccessResponse([
    'id' => 1,
    'name' => 'John Doe'
]);

// С кастомным статусом
return new SuccessResponse(
    data: ['message' => 'Created'],
    status: 201
);

// С заголовками
return new SuccessResponse(
    data: ['token' => 'abc123'],
    status: 200,
    headers: ['X-Custom-Header' => 'value']
);
```

**Примеры из проекта:**

```php
// AuthController - возврат токена
public function signin(SigninCommand $command, Signin $handler): Responsable
{
    $token = $handler->handle($command);

    return new SuccessResponse([
        "token" => $token
    ]);
}

// UserController - возврат данных пользователя
public function update(UpdateUserCommand $command, UpdateUser $handler)
{
    $result = $handler->handle($command, Auth::user());

    return new SuccessResponse($result);
}
```

## ErrorResponse

Используется для ответов с ошибками (если реализован).

**Предполагаемое использование:**

```php
use App\Shared\Responses\Api\ErrorResponse;

return new ErrorResponse(
    message: 'Resource not found',
    status: 404
);

// С дополнительными данными
return new ErrorResponse(
    message: 'Validation failed',
    status: 422,
    errors: [
        'email' => ['Email is required'],
        'password' => ['Password must be at least 8 characters']
    ]
);
```

## Создание кастомных Response классов

### Пример 1: PaginatedResponse

```php
<?php

namespace App\Shared\Responses\Api;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedResponse extends BaseResponse
{
    public function __construct(LengthAwarePaginator $paginator)
    {
        $data = [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ]
        ];

        parent::__construct($data);
    }
}
```

**Использование:**

```php
use App\Shared\Responses\Api\PaginatedResponse;

public function index(): Responsable
{
    $users = User::paginate(15);
    
    return new PaginatedResponse($users);
}
```

### Пример 2: CreatedResponse

```php
<?php

namespace App\Shared\Responses\Api;

class CreatedResponse extends BaseResponse
{
    public function __construct(mixed $data, string|null $location = null)
    {
        $headers = $location ? ['Location' => $location] : [];
        
        parent::__construct($data, 201, $headers);
    }
}
```

**Использование:**

```php
use App\Shared\Responses\Api\CreatedResponse;

public function store(CreateUserCommand $command, CreateUser $handler): Responsable
{
    $user = $handler->handle($command);
    
    return new CreatedResponse(
        data: ['id' => $user->id, 'name' => $user->name],
        location: route('api.users.show', $user->id)
    );
}
```

### Пример 3: NoContentResponse

```php
<?php

namespace App\Shared\Responses\Api;

class NoContentResponse extends BaseResponse
{
    public function __construct()
    {
        parent::__construct(null, 204);
    }
}
```

**Использование:**

```php
use App\Shared\Responses\Api\NoContentResponse;

public function delete(int $id, DeleteUser $handler): Responsable
{
    $handler->handle($id);
    
    return new NoContentResponse();
}
```

## Обработка исключений

### ValidationException

Laravel автоматически обрабатывает `ValidationException` и возвращает JSON с ошибками валидации:

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### LogicException и другие исключения

Для бизнес-логических ошибок можно создать кастомный обработчик в `app/Exceptions/Handler.php`:

```php
public function render($request, Throwable $exception)
{
    if ($exception instanceof LogicException) {
        return response()->json([
            'message' => $exception->getMessage()
        ], 400);
    }

    return parent::render($request, $exception);
}
```

## Стандартные HTTP статусы

### Успешные ответы (2xx)

- **200 OK** - Успешный запрос (по умолчанию в SuccessResponse)
- **201 Created** - Ресурс создан
- **204 No Content** - Успешно, но нет данных для возврата

### Клиентские ошибки (4xx)

- **400 Bad Request** - Некорректный запрос
- **401 Unauthorized** - Требуется аутентификация
- **403 Forbidden** - Доступ запрещен
- **404 Not Found** - Ресурс не найден
- **422 Unprocessable Entity** - Ошибка валидации

### Серверные ошибки (5xx)

- **500 Internal Server Error** - Внутренняя ошибка сервера

## Лучшие практики

### 1. Всегда используйте Response классы

**Плохо:**

```php
public function index()
{
    $users = User::all();
    return response()->json($users);
}
```

**Хорошо:**

```php
public function index(): Responsable
{
    $users = User::all();
    return new SuccessResponse($users);
}
```

### 2. Указывайте тип возврата

```php
public function method(): Responsable
{
    // ...
}
```

### 3. Структурируйте данные

**Плохо:**

```php
return new SuccessResponse($user);
```

**Хорошо:**

```php
return new SuccessResponse([
    'id' => $user->id,
    'name' => $user->name,
    'email' => $user->email,
    'created_at' => $user->created_at->toIso8601String(),
]);
```

### 4. Используйте Data Transfer Objects

```php
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
    ) {}
}

// В контроллере
return new SuccessResponse(
    UserData::from($user)
);
```

### 5. Создавайте специализированные Response классы

Для часто используемых форматов ответов создавайте отдельные классы вместо дублирования логики.

## Тестирование

```php
use App\Shared\Responses\Api\SuccessResponse;

test('returns success response', function () {
    $response = new SuccessResponse(['message' => 'OK']);
    
    $jsonResponse = $response->toResponse(request());
    
    expect($jsonResponse->getStatusCode())->toBe(200);
    expect($jsonResponse->getData(true))->toBe(['message' => 'OK']);
});

test('returns created response with location header', function () {
    $response = new CreatedResponse(
        ['id' => 1],
        'https://api.example.com/users/1'
    );
    
    $jsonResponse = $response->toResponse(request());
    
    expect($jsonResponse->getStatusCode())->toBe(201);
    expect($jsonResponse->headers->get('Location'))
        ->toBe('https://api.example.com/users/1');
});
```

## Интеграция с API Resources

Response классы хорошо работают с Laravel API Resources:

```php
use App\Http\Resources\UserResource;
use App\Shared\Responses\Api\SuccessResponse;

public function show(int $id): Responsable
{
    $user = User::findOrFail($id);
    
    return new SuccessResponse(
        new UserResource($user)
    );
}
```

## Заключение

Response классы обеспечивают:
- **Консистентность** - единый формат ответов
- **Переиспользование** - DRY принцип
- **Тестируемость** - легко тестировать
- **Расширяемость** - просто добавлять новые типы
