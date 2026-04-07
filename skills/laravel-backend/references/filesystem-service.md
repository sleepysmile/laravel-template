# Filesystem Service

Сервис для работы с файлами в проекте. Предоставляет три типа файловых систем с различными стратегиями хранения.

## Архитектура

```
Shared/Services/Filesystem/
├── Adapters/
│   ├── FileAdapter.php           # Адаптер для работы с файлами
│   └── FilesystemAdapter.php     # Адаптер для Laravel Storage
├── Contracts/
│   ├── FileNameStrategy.php      # Интерфейс стратегии имен
│   └── FilePathStrategy.php      # Интерфейс стратегии путей
├── FileNameStrategies/
│   └── UuidStrategy.php          # Генерация UUID имен
├── FilePathStrategies/
│   ├── ModelStrategy.php         # Путь на основе модели
│   ├── StaticDirectoryStrategy.php
│   ├── StaticExportStrategy.php  # Путь для экспорта
│   └── StaticImageStrategy.php   # Путь для изображений
├── ModelFilesystem.php           # ФС для файлов моделей
├── StaticFilesystem.php          # ФС для статических файлов
└── ResourceFilesystem.php        # ФС для ресурсов (не реализован)
```

## Типы файловых систем

### 1. ModelFilesystem

Используется для хранения файлов, привязанных к моделям (например, аватары пользователей).

**Особенности:**
- Автоматически обновляет атрибут модели с путем к файлу
- Удаляет старый файл при замене
- Использует стратегию путей на основе модели

**Пример использования:**

```php
use Illuminate\Support\Facades\Storage;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$storage = Storage::disk();
$adapter = new FilesystemAdapter($storage);
$fs = new ModelFilesystem($adapter);

// Сохранение файла из загруженного файла
$savedPath = $fs->store(
    $user,                                      // Модель
    'avatar',                                   // Атрибут модели
    FileAdapter::fromUploadedFile($uploadFile)  // Файл
);

// $user->avatar теперь содержит путь к файлу
```

**Методы:**

```php
// Сохранить файл и обновить модель
public function store(
    Model $model,
    string $attribute,
    FileAdapter $uploadedFile
): string

// Удалить файл модели
public function delete(Model $model, string $attribute): bool

// Установить стратегию пути
public function setPathStrategy(FilePathStrategy $pathStrategy): self

// Установить стратегию имени
public function setNameStrategy(FileNameStrategy $nameStrategy): self
```

### 2. StaticFilesystem

Используется для хранения статических файлов (изображения, документы и т.д.).

**Особенности:**
- Не привязан к моделям
- Гибкие стратегии именования и путей
- Может удалять старые файлы при замене

**Пример использования:**

```php
use Illuminate\Support\Facades\Storage;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\StaticFilesystem;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$storage = Storage::disk();
$adapter = new FilesystemAdapter($storage);
$fs = new StaticFilesystem($adapter);

// Сохранение нового файла
$savedPath = $fs->store(
    FileAdapter::fromUploadedFile($uploadFile)
);

// Замена существующего файла
$savedPath = $fs->store(
    FileAdapter::fromUploadedFile($uploadFile),
    $oldFilePath  // Старый файл будет удален
);

// Удаление файла
$fs->delete($filePath);
```

**Методы:**

```php
// Сохранить файл
public function store(
    FileAdapter $uploadedFile,
    string|null $oldFilePath = null
): string

// Удалить файл
public function delete(string $path): bool

// Установить стратегию пути
public function setPathStrategy(FilePathStrategy $pathStrategy): self

// Установить стратегию имени
public function setNameStrategy(FileNameStrategy $nameStrategy): self
```

### 3. ResourceFilesystem

Для работы с файловыми ресурсами (потоками).

**Статус:** Частично реализован (методы выбрасывают `LogicException`)

## FileAdapter

Адаптер для работы с файлами из различных источников.

**Создание из загруженного файла:**

```php
use Illuminate\Http\UploadedFile;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$adapter = FileAdapter::fromUploadedFile($uploadedFile);
```

**Методы:**

```php
// Получить содержимое файла
public function content(): string

// Получить расширение файла
public function extension(): string

// Получить оригинальное имя
public function originalName(): string

// Получить MIME тип
public function mimeType(): string

// Получить размер в байтах
public function size(): int
```

## Стратегии путей (FilePathStrategy)

### ModelStrategy

Генерирует путь на основе модели и атрибута.

```php
use App\Shared\Services\Filesystem\FilePathStrategies\ModelStrategy;

$strategy = new ModelStrategy($model, 'avatar');
// Результат: "users/{model_id}/avatar/"
```

### StaticImageStrategy

Путь для статических изображений.

```php
use App\Shared\Services\Filesystem\FilePathStrategies\StaticImageStrategy;

$strategy = new StaticImageStrategy();
// Результат: "images/"
```

### StaticExportStrategy

Путь для экспортированных файлов.

```php
use App\Shared\Services\Filesystem\FilePathStrategies\StaticExportStrategy;

$strategy = new StaticExportStrategy();
// Результат: "exports/"
```

### StaticDirectoryStrategy

Кастомная директория.

```php
use App\Shared\Services\Filesystem\FilePathStrategies\StaticDirectoryStrategy;

$strategy = new StaticDirectoryStrategy('documents');
// Результат: "documents/"
```

## Стратегии имен (FileNameStrategy)

### UuidStrategy

Генерирует уникальное имя на основе UUID.

```php
use App\Shared\Services\Filesystem\FileNameStrategies\UuidStrategy;

$strategy = new UuidStrategy();
// Результат: "550e8400-e29b-41d4-a716-446655440000"
```

## Создание кастомных стратегий

### Кастомная стратегия пути

```php
<?php

namespace App\Shared\Services\Filesystem\FilePathStrategies;

use App\Shared\Services\Filesystem\Contracts\FilePathStrategy;

class CustomPathStrategy implements FilePathStrategy
{
    public function path(): string
    {
        return "custom/path/";
    }
}
```

### Кастомная стратегия имени

```php
<?php

namespace App\Shared\Services\Filesystem\FileNameStrategies;

use App\Shared\Services\Filesystem\Contracts\FileNameStrategy;

class TimestampStrategy implements FileNameStrategy
{
    public function name(): string
    {
        return (string) time();
    }
}
```

## Полный пример: Обновление аватара пользователя

```php
<?php

namespace App\Api\Handlers\User;

use App\Shared\Models\User;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateUserAvatar
{
    public function handle(User $user, UploadedFile $avatar): string
    {
        $storage = Storage::disk();
        $adapter = new FilesystemAdapter($storage);
        $fs = new ModelFilesystem($adapter);

        // Автоматически удалит старый аватар и обновит $user->avatar
        $savedPath = $fs->store(
            $user,
            'avatar',
            FileAdapter::fromUploadedFile($avatar)
        );

        return $savedPath;
    }
}
```

## Тестирование

```php
<?php

use App\Shared\Models\User;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\Adapters\FilesystemAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('can upload user avatar', function () {
    Storage::fake();
    
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg');
    
    $adapter = new FilesystemAdapter(Storage::disk());
    $fs = new ModelFilesystem($adapter);
    
    $path = $fs->store($user, 'avatar', FileAdapter::fromUploadedFile($file));
    
    expect($path)->not->toBeNull();
    expect($user->avatar)->toBe($path);
    Storage::disk()->assertExists($path);
});
```

## Важные замечания

1. **Всегда используйте FileAdapter** для работы с файлами
2. **ModelFilesystem автоматически сохраняет модель** после обновления атрибута
3. **Стратегии можно комбинировать** для создания сложных путей
4. **ResourceFilesystem не реализован** - используйте ModelFilesystem или StaticFilesystem
5. **Пути всегда заканчиваются на `/`** в стратегиях путей
