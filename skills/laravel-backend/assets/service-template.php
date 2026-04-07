<?php

namespace App\Shared\Services\[ServiceName];

/**
 * Шаблон сервиса
 * 
 * Сервисы используются для:
 * - Переиспользуемой бизнес-логики
 * - Интеграций с внешними API
 * - Сложных операций с данными
 * - Работы с файлами, email и т.д.
 */
class [ServiceName]
{
    /**
     * Конструктор для dependency injection
     */
    public function __construct(
        // private SomeDependency $dependency,
    ) {}

    /**
     * Основной метод сервиса
     */
    public function execute(mixed $input): mixed
    {
        // Реализация логики
        
        return $result;
    }

    /**
     * Вспомогательные protected методы
     */
    protected function helper(): void
    {
        // Вспомогательная логика
    }
}

/**
 * Пример использования в Handler:
 * 
 * class SomeHandler
 * {
 *     public function handle(SomeCommand $command, [ServiceName] $service): mixed
 *     {
 *         $result = $service->execute($command->data);
 *         return $result;
 *     }
 * }
 */

/**
 * Если сервис требует сложной настройки, создайте Service Provider:
 * 
 * php artisan make:provider [ServiceName]ServiceProvider
 * 
 * В Provider:
 * 
 * public function register(): void
 * {
 *     $this->app->singleton([ServiceName]::class, function ($app) {
 *         return new [ServiceName](
 *             // dependencies
 *         );
 *     });
 * }
 */
