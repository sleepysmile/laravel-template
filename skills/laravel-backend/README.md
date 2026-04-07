# Laravel Backend Skill

Специализированный skill для работы с Laravel 12 бэкендом проекта fullstack_test.

## Описание

Этот skill предоставляет комплексное руководство по работе с бэкендом на Laravel 12, использующим архитектуру на основе Command-Handler паттерна. Skill включает документацию, скрипты автоматизации и шаблоны для быстрой разработки.

## Структура

```
laravel-backend/
├── SKILL.MD                    # Основное руководство
├── references/                 # Справочная документация
│   ├── architecture-overview.md    # Обзор архитектуры
│   ├── filesystem-service.md       # Работа с файлами
│   ├── response-classes.md         # Классы ответов
│   └── testing-guide.md            # Руководство по тестированию
├── scripts/                    # Скрипты автоматизации
│   ├── create-api-endpoint.sh      # Создание API эндпоинта
│   └── README.md
└── assets/                     # Шаблоны
    ├── migration-template.php      # Шаблон миграции
    ├── model-template.php          # Шаблон модели
    ├── service-template.php        # Шаблон сервиса
    └── README.md
```

## Быстрый старт

### 1. Создание нового API эндпоинта

```bash
./skills/laravel-backend/scripts/create-api-endpoint.sh Post Create
```

Это создаст:
- Command класс
- Handler класс
- Controller (если не существует)

### 2. Основной рабочий процесс

1. **Прочитайте SKILL.MD** для понимания архитектуры
2. **Используйте scripts/** для автоматизации
3. **Обращайтесь к references/** для деталей
4. **Копируйте из assets/** шаблоны

## Ключевые концепции

### Command-Handler Pattern

```php
// Command (данные)
class UpdateUserCommand extends Data
{
    public function __construct(
        public string $name,
    ) {}
}

// Handler (логика)
class UpdateUser
{
    public function handle(UpdateUserCommand $command, User $user): array
    {
        $user->name = $command->name;
        $user->save();
        return ['id' => $user->id];
    }
}

// Controller (координация)
class UserController extends BaseClientController
{
    public function update(UpdateUserCommand $command, UpdateUser $handler)
    {
        $result = $handler->handle($command, Auth::user());
        return new SuccessResponse($result);
    }
}
```

## Технологический стек

- **PHP 8.3** - современные возможности языка
- **Laravel 12.0** - фреймворк
- **Docker + Nginx + PHP-FPM** - инфраструктура
- **Laravel Sanctum** - API аутентификация
- **Spatie Laravel Data** - DTO
- **Pest 4.3** - тестирование
- **PHPStan (уровень 5)** - статический анализ
- **PostgreSQL** - база данных
- **Redis** - кэш и очереди

## Полезные команды

**Важно:** Все PHP команды выполняются через sh-скрипты в корне проекта.

```bash
# Docker контейнеры
make up                    # Запуск
make down                  # Остановка
make restart               # Перезапуск
make build                 # Пересборка

# Artisan (через ./artisan)
./artisan test             # Запуск тестов
./artisan migrate          # Миграции
./artisan make:migration [name]
./artisan make:command [name]
./artisan pail             # Просмотр логов
./artisan tinker           # REPL консоль

# Composer (через ./composer)
./composer install         # Установка зависимостей
./composer require [pkg]   # Добавление пакета

# Статический анализ (через ./phpstan)
./phpstan analyse          # Запуск PHPStan

# База данных (через ./psql)
./psql                     # PostgreSQL консоль
```

## Документация

### Основная документация (SKILL.MD)

- Архитектурные принципы
- Структура проекта
- Рабочие процессы (API, модели, сервисы, команды)
- Стандарты кодирования
- Troubleshooting

### Справочные материалы (references/)

- **architecture-overview.md** - детальный обзор архитектуры
- **filesystem-service.md** - работа с файловой системой
- **response-classes.md** - стандартизированные ответы
- **testing-guide.md** - написание тестов с Pest

### Скрипты (scripts/)

- **create-api-endpoint.sh** - автоматическое создание API эндпоинта

### Шаблоны (assets/)

- **migration-template.php** - шаблон миграции с примерами
- **model-template.php** - шаблон Eloquent модели
- **service-template.php** - шаблон сервиса

## Примеры использования

### Создание нового эндпоинта для постов

```bash
# 1. Создаем структуру
./skills/laravel-backend/scripts/create-api-endpoint.sh Post Create

# 2. Редактируем Command
# php/app/Api/Handlers/Post/CreateCommand.php
class CreateCommand extends Data
{
    public function __construct(
        public string $title,
        public string $content,
    ) {}
}

# 3. Реализуем Handler
# php/app/Api/Handlers/Post/Create.php
public function handle(CreateCommand $command): array
{
    $this->validate($command);
    
    $post = Post::create([
        'title' => $command->title,
        'content' => $command->content,
    ]);
    
    return ['id' => $post->id];
}

# 4. Добавляем маршрут
# php/app/Api/routes.php
Route::post('/posts', [PostController::class, 'create']);
```

## Лучшие практики

1. ✅ Всегда используйте Command-Handler паттерн
2. ✅ Валидация в Handler, не в Controller
3. ✅ Типизируйте все параметры и возвращаемые значения
4. ✅ Используйте Response классы для ответов
5. ✅ Пишите тесты для критичной логики
6. ✅ Документируйте сложные части кода
7. ✅ Следуйте PSR-12 стандарту

## Поддержка

При возникновении вопросов:

1. Проверьте **SKILL.MD** для основной информации
2. Изучите **references/** для детальной документации
3. Посмотрите существующий код в проекте как примеры
4. Используйте **assets/** шаблоны как отправную точку

## Версия

- **Skill Version**: 1.0.0
- **Laravel Version**: 12.0
- **PHP Version**: 8.3
- **Last Updated**: 2026-04-07
