# Scripts

Вспомогательные скрипты для ускорения разработки.

## create-api-endpoint.sh

Автоматически создает структуру для нового API эндпоинта.

**Использование:**

```bash
./skills/laravel-backend/scripts/create-api-endpoint.sh FeatureName ActionName
```

**Пример:**

```bash
./skills/laravel-backend/scripts/create-api-endpoint.sh Post Create
```

**Что создается:**

1. `php/app/Api/Handlers/Post/CreateCommand.php` - Command класс
2. `php/app/Api/Handlers/Post/Create.php` - Handler класс
3. `php/app/Api/Controllers/PostController.php` - Controller (если не существует)

**После выполнения:**

1. Добавьте поля в Command класс
2. Добавьте правила валидации в Handler
3. Реализуйте бизнес-логику в Handler::handle()
4. Добавьте маршрут в `php/app/Api/routes.php`
5. Добавьте use для контроллера в routes.php

**Примечание:** Если контроллер уже существует, скрипт выведет код метода, который нужно добавить вручную.
