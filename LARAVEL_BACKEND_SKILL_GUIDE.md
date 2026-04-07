# 🎯 Laravel Backend Skill - Руководство по использованию

## 📦 Что было создано

Я создал полноценный **Skill** для работы с вашим Laravel бэкендом. Skill следует стандартам architector и содержит всё необходимое для эффективной разработки.

## 📂 Структура Skill

```
skills/laravel-backend/
├── SKILL.MD                           # 📘 Основное руководство (главный файл)
├── README.md                          # 📖 Описание skill
│
├── references/                        # 📚 Справочная документация
│   ├── architecture-overview.md       # Обзор архитектуры проекта
│   ├── filesystem-service.md          # Работа с файловой системой
│   ├── response-classes.md            # Классы ответов API
│   └── testing-guide.md               # Руководство по тестированию
│
├── scripts/                           # 🔧 Скрипты автоматизации
│   ├── create-api-endpoint.sh         # Создание API эндпоинта
│   └── README.md
│
└── assets/                            # 📄 Шаблоны кода
    ├── migration-template.php         # Шаблон миграции
    ├── model-template.php             # Шаблон модели
    ├── service-template.php           # Шаблон сервиса
    └── README.md
```

**Всего:** 14 файлов, ~180KB документации

## 🚀 Быстрый старт

### 1. Создание нового API эндпоинта

```bash
./skills/laravel-backend/scripts/create-api-endpoint.sh Post Create
```

Это автоматически создаст:
- ✅ Command класс (`CreateCommand.php`)
- ✅ Handler класс (`Create.php`)
- ✅ Controller (`PostController.php`) - если не существует
- ✅ Подскажет, какой маршрут добавить

### 2. Использование шаблонов

```bash
# Скопировать шаблон миграции
cp skills/laravel-backend/assets/migration-template.php php/database/migrations/[your_migration].php

# Скопировать шаблон модели
cp skills/laravel-backend/assets/model-template.php php/app/Shared/Models/YourModel.php
```

## 📖 Что включено в Skill

### 1. SKILL.MD - Основное руководство

Содержит:
- ✅ Архитектурные принципы (Command-Handler Pattern)
- ✅ Структура проекта
- ✅ Пошаговые рабочие процессы:
  - Создание API эндпоинта
  - Работа с моделями
  - Создание сервисов
  - Создание консольных команд
  - Работа с миграциями
  - Аутентификация через Sanctum
- ✅ Стандарты кодирования
- ✅ Технологический стек
- ✅ Полезные команды
- ✅ Troubleshooting

### 2. References - Детальная документация

#### shell-scripts.md
- Работа с ./artisan, ./composer, ./phpstan, ./psql
- Makefile команды (make up, make down, etc.)
- Рабочие процессы (setup, разработка, деплой)
- Устранение проблем
- Лучшие практики

#### php83-standards.md
- **ПОЛНОЕ руководство по PHP 8.3**
- Constructor property promotion
- Readonly properties
- Union types, Enums, Match expressions
- Named arguments, Null safe operator
- Типизация (параметры, возвращаемые значения)
- PSR-12 форматирование
- PHPDoc аннотации
- Лучшие практики и чеклист

#### architecture-overview.md
- Command-Handler Pattern в деталях
- Поток данных в приложении
- Dependency Injection
- Валидация
- Docker + Nginx + PHP-FPM
- Лучшие практики и антипаттерны

#### filesystem-service.md
- Работа с ModelFilesystem (файлы моделей)
- Работа с StaticFilesystem (статические файлы)
- FileAdapter и стратегии
- Примеры использования
- Тестирование файловых операций

#### response-classes.md
- Все типы Response классов
- Создание кастомных Response
- Обработка исключений
- Стандартные HTTP статусы
- Лучшие практики

#### testing-guide.md
- Feature тесты (API)
- Unit тесты (Handler, Services)
- Использование Pest
- Фабрики и fixtures
- Матчеры и assertions
- Покрытие кода

### 3. Scripts - Автоматизация

#### create-api-endpoint.sh
Автоматически создает полную структуру для нового API эндпоинта:
- Command класс с шаблоном
- Handler класс с валидацией
- Controller с методом
- Выводит инструкции по добавлению маршрута

### 4. Assets - Шаблоны

#### migration-template.php
- Примеры всех типов полей
- Внешние ключи
- Индексы
- Комментарии с объяснениями

#### model-template.php
- PHPDoc аннотации
- Fillable и hidden поля
- Casts
- Relationships (hasMany, belongsTo, etc.)
- Scopes
- Accessors и Mutators

#### service-template.php
- Базовая структура сервиса
- Dependency Injection
- Примеры использования

## 💡 Примеры использования

### Пример 1: Создание эндпоинта для постов

```bash
# 1. Создаем структуру
./skills/laravel-backend/scripts/create-api-endpoint.sh Post Create

# 2. Редактируем Command (php/app/Api/Handlers/Post/CreateCommand.php)
class CreateCommand extends Data
{
    public function __construct(
        public string $title,
        public string $content,
    ) {}
}

# 3. Реализуем Handler (php/app/Api/Handlers/Post/Create.php)
protected function validate(CreateCommand $command): void
{
    $validator = Validator::make($command->toArray(), [
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string'],
    ]);
    // ...
}

public function handle(CreateCommand $command): array
{
    $this->validate($command);
    
    $post = Post::create([
        'title' => $command->title,
        'content' => $command->content,
    ]);
    
    return ['id' => $post->id, 'title' => $post->title];
}

# 4. Добавляем маршрут (php/app/Api/routes.php)
Route::post('/posts', [PostController::class, 'create']);
```

### Пример 2: Работа с файлами

```php
// См. references/filesystem-service.md для полной документации

use App\Shared\Services\Filesystem\ModelFilesystem;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;

$fs = new ModelFilesystem($adapter);
$path = $fs->store($user, 'avatar', FileAdapter::fromUploadedFile($file));
```

### Пример 3: Написание тестов

```php
// См. references/testing-guide.md для полной документации

test('user can create post', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    
    $response = postJson('/api/posts', [
        'title' => 'Test Post',
        'content' => 'Content',
    ]);
    
    $response->assertStatus(200);
});
```

## 🎓 Как использовать Skill

### Для AI-агента

Skill предназначен для использования AI-агентом (Claude). Когда вы работаете с бэкендом:

1. AI автоматически загрузит `SKILL.MD` при работе с бэкендом
2. AI будет обращаться к `references/` для детальной информации
3. AI будет использовать `scripts/` для автоматизации
4. AI будет копировать из `assets/` шаблоны

### Для разработчика

Вы можете:

1. **Читать документацию** в `SKILL.MD` и `references/`
2. **Использовать скрипты** из `scripts/` для автоматизации
3. **Копировать шаблоны** из `assets/` как отправную точку
4. **Следовать примерам** из существующего кода проекта

## 📋 Ключевые концепции

### 1. PHP 8.3 Стандарты

```php
<?php

declare(strict_types=1); // ВСЕГДА

// ✅ Constructor property promotion
class UpdateUserCommand extends Data
{
    public function __construct(
        public string $name,
        public readonly int $userId,
    ) {}
}

// ✅ Union types & Named arguments
public function find(string|int $id): User|null
{
    return User::find($id);
}

$command = new UpdateUserCommand(name: 'John', userId: 123);
```

### 2. Command-Handler Pattern

```php
// Command - только данные
class UpdateUserCommand extends Data {
    public function __construct(public string $name) {}
}

// Handler - только логика
class UpdateUser {
    public function handle(UpdateUserCommand $command, User $user): array {
        $user->name = $command->name;
        $user->save();
        return ['id' => $user->id];
    }
}

// Controller - только координация
class UserController {
    public function update(UpdateUserCommand $command, UpdateUser $handler): Responsable {
        $result = $handler->handle($command, Auth::user());
        return new SuccessResponse($result);
    }
}
```

### 3. CLI Commands - Формат имени: `x:{model|source}:{action}`

```php
// ✅ Правильно
protected $signature = 'app:user:create {email} {name}';
protected $signature = 'app:export:users {--format=csv}';

// ❌ Неправильно
protected $signature = 'create-user';
```

### 4. Shell Scripts

```bash
./artisan migrate    # Вместо docker-compose exec app php artisan migrate
./composer install   # Вместо docker-compose exec app composer install
./phpstan analyse    # Статический анализ
make up / make down  # Docker контейнеры
```

## 🔍 Навигация по Skill

**Начните с:**
- `SKILL.MD` - для общего понимания

**Затем изучите:**
- `references/architecture-overview.md` - для понимания архитектуры
- `references/testing-guide.md` - для написания тестов

**Используйте по необходимости:**
- `references/shell-scripts.md` - работа с ./artisan, ./composer, ./phpstan
- `references/php83-standards.md` - стандарты PHP 8.3 (обязательно к изучению!)
- `references/filesystem-service.md` - при работе с файлами
- `references/response-classes.md` - при создании ответов
- `scripts/create-api-endpoint.sh` - для создания эндпоинтов
- `assets/*` - для шаблонов

## ✅ Преимущества Skill

1. **Полнота** - покрывает все аспекты разработки бэкенда
2. **Структурированность** - четкая организация информации
3. **Практичность** - реальные примеры из вашего проекта
4. **Автоматизация** - скрипты для рутинных задач
5. **Шаблоны** - готовые заготовки кода
6. **Актуальность** - специфичен для вашей архитектуры

## 🎯 Следующие шаги

1. ✅ **Skill создан** - `skills/laravel-backend/`
2. 📖 **Изучите SKILL.MD** - основное руководство
3. 🧪 **Попробуйте скрипт** - создайте тестовый эндпоинт
4. 📚 **Изучите references** - детальная документация
5. 🚀 **Начните разработку** - используйте skill в работе

## 📞 Поддержка

При работе с бэкендом:
- Обращайтесь к `SKILL.MD` для основной информации
- Используйте `references/` для деталей
- Запускайте `scripts/` для автоматизации
- Копируйте `assets/` для шаблонов

---

**Skill готов к использованию!** 🎉

Теперь AI-агент (и вы) имеете полное руководство по работе с вашим Laravel бэкендом.
