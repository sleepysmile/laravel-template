# Shell Scripts - Работа с проектом

Руководство по использованию sh-скриптов для работы с проектом в Docker окружении.

## Обзор

Проект использует sh-скрипты в корне для выполнения команд внутри Docker контейнеров. Это обеспечивает единообразный способ работы с проектом.

## Доступные скрипты

### ./artisan

Выполняет команды Laravel Artisan внутри Docker контейнера.

**Расположение:** `/home/roman/Project/home/php/fullstack_test/artisan`

**Использование:**

```bash
./artisan [command] [arguments] [options]
```

**Примеры:**

```bash
# Миграции
./artisan migrate
./artisan migrate:fresh
./artisan migrate:rollback
./artisan migrate:status

# Создание компонентов
./artisan make:migration create_posts_table
./artisan make:model Post
./artisan make:controller PostController
./artisan make:command CreateUser

# Кэш
./artisan cache:clear
./artisan config:clear
./artisan route:clear
./artisan view:clear

# Оптимизация
./artisan config:cache
./artisan route:cache
./artisan view:cache

# Тестирование
./artisan test
./artisan test --filter UserTest
./artisan test --coverage

# Утилиты
./artisan route:list           # Список маршрутов
./artisan tinker               # REPL консоль
./artisan pail                 # Просмотр логов в реальном времени
./artisan queue:work           # Обработка очереди
./artisan schedule:run         # Запуск планировщика

# Генерация ключа
./artisan key:generate

# Seeding
./artisan db:seed
./artisan db:seed --class=UserSeeder
```

**Кастомные команды:**

Формат имени: `x:{model|source}:{action}`

```bash
# Создание пользователя
./artisan app:user:create john@example.com "John Doe" password123

# Экспорт данных
./artisan app:export:users --format=csv

# Очистка старых данных
./artisan app:cleanup:old-sessions
```

### ./composer

Выполняет команды Composer внутри Docker контейнера.

**Расположение:** `/home/roman/Project/home/php/fullstack_test/composer`

**Использование:**

```bash
./composer [command] [arguments] [options]
```

**Примеры:**

```bash
# Установка зависимостей
./composer install
./composer install --no-dev          # Без dev-зависимостей
./composer install --optimize-autoloader

# Обновление зависимостей
./composer update
./composer update vendor/package     # Конкретный пакет
./composer update --with-dependencies

# Добавление пакетов
./composer require vendor/package
./composer require --dev phpunit/phpunit

# Удаление пакетов
./composer remove vendor/package

# Информация
./composer show                      # Все пакеты
./composer show vendor/package       # Информация о пакете
./composer outdated                  # Устаревшие пакеты

# Валидация
./composer validate
./composer diagnose

# Autoload
./composer dump-autoload
./composer dump-autoload --optimize

# Скрипты (из composer.json)
./composer run-script [script-name]
```

### ./phpstan

Выполняет статический анализ кода с PHPStan.

**Расположение:** `/home/roman/Project/home/php/fullstack_test/phpstan`

**Использование:**

```bash
./phpstan [command] [arguments] [options]
```

**Примеры:**

```bash
# Анализ всего проекта
./phpstan analyse

# Анализ конкретной директории
./phpstan analyse app/Api/

# Анализ конкретного файла
./phpstan analyse app/Api/Controllers/UserController.php

# С указанием уровня (0-9)
./phpstan analyse --level 5

# Очистка кэша
./phpstan clear-result-cache

# Генерация baseline (игнорирование текущих ошибок)
./phpstan analyse --generate-baseline
```

**Конфигурация:**

Настройки в файле `php/phpstan.neon`:

```neon
parameters:
    paths:
        - app/
    level: 5
```

### ./psql

Подключение к PostgreSQL консоли.

**Расположение:** `/home/roman/Project/home/php/fullstack_test/psql`

**Использование:**

```bash
./psql
```

**Примеры команд в psql:**

```sql
-- Список баз данных
\l

-- Подключение к базе
\c database_name

-- Список таблиц
\dt

-- Описание таблицы
\d table_name

-- Выполнение SQL
SELECT * FROM users;

-- Выход
\q
```

## Makefile команды

Дополнительно доступны команды через Makefile для управления Docker.

**Расположение:** `/home/roman/Project/home/php/fullstack_test/Makefile`

**Использование:**

```bash
make [command]
```

**Доступные команды:**

```bash
# Запуск контейнеров
make up

# Остановка контейнеров
make down

# Пересборка образов
make build

# Перезапуск (down + up)
make restart
```

## Рабочие процессы

### Первоначальная настройка проекта

```bash
# 1. Копирование конфигурации
cp ./php/.env.local ./php/.env
cp ./docker/docker-compose.local.yml ./docker-compose.yml

# 2. Сборка контейнеров
make build

# 3. Запуск контейнеров
make up

# 4. Установка зависимостей
./composer install

# 5. Генерация ключа приложения
./artisan key:generate

# 6. Запуск миграций
./artisan migrate

# 7. (Опционально) Заполнение тестовыми данными
./artisan db:seed
```

### Ежедневная разработка

```bash
# Запуск проекта
make up

# Просмотр логов
./artisan pail

# Запуск тестов
./artisan test

# Статический анализ
./phpstan analyse

# Остановка проекта
make down
```

### Работа с миграциями

```bash
# Создание миграции
./artisan make:migration create_posts_table

# Редактирование файла миграции
# php/database/migrations/YYYY_MM_DD_HHMMSS_create_posts_table.php

# Запуск миграций
./artisan migrate

# Откат последней миграции
./artisan migrate:rollback

# Откат всех миграций и повторный запуск
./artisan migrate:fresh

# Проверка статуса миграций
./artisan migrate:status
```

### Работа с моделями

```bash
# Создание модели
./artisan make:model Post

# Создание модели с миграцией
./artisan make:model Post -m

# Создание модели с миграцией и фабрикой
./artisan make:model Post -mf
```

### Работа с зависимостями

```bash
# Добавление нового пакета
./composer require vendor/package

# Обновление зависимостей
./composer update

# Проверка устаревших пакетов
./composer outdated

# Удаление пакета
./composer remove vendor/package
```

### Тестирование

```bash
# Запуск всех тестов
./artisan test

# Запуск конкретного теста
./artisan test --filter test_user_can_signin

# Запуск тестов из файла
./artisan test tests/Feature/AuthTest.php

# С покрытием кода
./artisan test --coverage

# Параллельное выполнение
./artisan test --parallel
```

### Отладка

```bash
# Просмотр логов в реальном времени
./artisan pail

# REPL консоль
./artisan tinker

# Список маршрутов
./artisan route:list

# Информация о конфигурации
./artisan config:show

# Проверка подключения к БД
./psql
```

### Очистка кэша

```bash
# Очистка всего кэша
./artisan cache:clear
./artisan config:clear
./artisan route:clear
./artisan view:clear

# Или все сразу
./artisan optimize:clear
```

### Оптимизация для продакшена

```bash
# Кэширование конфигурации
./artisan config:cache

# Кэширование маршрутов
./artisan route:cache

# Кэширование представлений
./artisan view:cache

# Оптимизация autoloader
./composer dump-autoload --optimize
```

## Устранение проблем

### Проблема: Скрипт не выполняется

**Ошибка:** `Permission denied`

**Решение:**

```bash
chmod +x ./artisan
chmod +x ./composer
chmod +x ./phpstan
chmod +x ./psql
```

### Проблема: Контейнер не запущен

**Ошибка:** `Cannot connect to Docker daemon`

**Решение:**

```bash
# Проверьте статус Docker
docker ps

# Запустите контейнеры
make up

# Проверьте логи
docker-compose logs app
```

### Проблема: Изменения не применяются

**Решение:**

```bash
# Очистите кэш Laravel
./artisan cache:clear
./artisan config:clear

# Перезапустите контейнеры
make restart

# Пересоберите контейнеры (если изменились Dockerfile)
make build
make up
```

### Проблема: База данных недоступна

**Решение:**

```bash
# Проверьте статус контейнера PostgreSQL
docker-compose ps postgres

# Проверьте логи
docker-compose logs postgres

# Подключитесь к БД
./psql

# Проверьте .env файл
cat php/.env | grep DB_
```

## Лучшие практики

### 1. Всегда используйте скрипты

**✅ Хорошо:**
```bash
./artisan migrate
./composer install
```

**❌ Плохо:**
```bash
docker-compose exec app php artisan migrate
docker-compose exec app composer install
```

### 2. Проверяйте статус перед работой

```bash
# Проверьте, что контейнеры запущены
docker-compose ps

# Если нет - запустите
make up
```

### 3. Очищайте кэш после изменений конфигурации

```bash
# После изменения .env или config файлов
./artisan config:clear
```

### 4. Используйте Makefile для Docker операций

```bash
# Вместо docker-compose up -d
make up

# Вместо docker-compose down
make down
```

### 5. Регулярно проверяйте код

```bash
# Перед коммитом
./phpstan analyse
./artisan test
```

## Создание собственных скриптов

Если нужно создать новый скрипт:

```bash
#!/bin/bash

# Пример: ./mycommand
docker-compose exec app php artisan my:command "$@"
```

Сделайте исполняемым:

```bash
chmod +x ./mycommand
```

## Заключение

Использование sh-скриптов обеспечивает:
- **Единообразие** - один способ выполнения команд
- **Простоту** - не нужно помнить docker-compose команды
- **Скорость** - короткие команды вместо длинных
- **Надежность** - команды выполняются в правильном контейнере
