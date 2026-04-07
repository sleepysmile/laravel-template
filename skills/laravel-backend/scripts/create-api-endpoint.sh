#!/bin/bash

# Скрипт для быстрого создания нового API эндпоинта
# Использование: ./create-api-endpoint.sh FeatureName ActionName

set -e

if [ "$#" -ne 2 ]; then
    echo "Использование: $0 FeatureName ActionName"
    echo "Пример: $0 Post Create"
    exit 1
fi

FEATURE=$1
ACTION=$2
FEATURE_LOWER=$(echo "$FEATURE" | tr '[:upper:]' '[:lower:]')
ACTION_LOWER=$(echo "$ACTION" | tr '[:upper:]' '[:lower:]')

BASE_DIR="php/app/Api"
HANDLERS_DIR="$BASE_DIR/Handlers/$FEATURE"
CONTROLLERS_DIR="$BASE_DIR/Controllers"

echo "Создание структуры для $FEATURE::$ACTION..."

# Создаем директорию для handlers
mkdir -p "$HANDLERS_DIR"

# Создаем Command класс
cat > "$HANDLERS_DIR/${ACTION}Command.php" << EOF
<?php

namespace App\Api\Handlers\\$FEATURE;

use Spatie\LaravelData\Data;

class ${ACTION}Command extends Data
{
    public function __construct(
        // Добавьте поля здесь
        // public string \$field,
    ) {}
}
EOF

echo "✓ Создан Command: $HANDLERS_DIR/${ACTION}Command.php"

# Создаем Handler класс
cat > "$HANDLERS_DIR/${ACTION}.php" << EOF
<?php

namespace App\Api\Handlers\\$FEATURE;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class $ACTION
{
    protected function validate(${ACTION}Command \$command): void
    {
        \$validator = Validator::make(\$command->toArray(), [
            // Добавьте правила валидации здесь
            // 'field' => ['required', 'string'],
        ]);

        if (\$validator->fails()) {
            throw new ValidationException(\$validator);
        }
    }

    public function handle(${ACTION}Command \$command): mixed
    {
        \$this->validate(\$command);

        // TODO: Реализуйте бизнес-логику здесь

        return [];
    }
}
EOF

echo "✓ Создан Handler: $HANDLERS_DIR/${ACTION}.php"

# Проверяем, существует ли контроллер
CONTROLLER_FILE="$CONTROLLERS_DIR/${FEATURE}Controller.php"

if [ ! -f "$CONTROLLER_FILE" ]; then
    # Создаем новый контроллер
    cat > "$CONTROLLER_FILE" << EOF
<?php

namespace App\Api\Controllers;

use App\Api\Handlers\\$FEATURE\\$ACTION;
use App\Api\Handlers\\$FEATURE\\${ACTION}Command;
use App\Shared\Responses\Api\SuccessResponse;
use Illuminate\Contracts\Support\Responsable;

class ${FEATURE}Controller extends BaseClientController
{
    public function ${ACTION_LOWER}(${ACTION}Command \$command, $ACTION \$handler): Responsable
    {
        \$result = \$handler->handle(\$command);

        return new SuccessResponse(\$result);
    }
}
EOF
    echo "✓ Создан Controller: $CONTROLLER_FILE"
else
    # Добавляем метод в существующий контроллер
    echo ""
    echo "⚠ Контроллер $CONTROLLER_FILE уже существует."
    echo "Добавьте следующий метод вручную:"
    echo ""
    cat << EOF
    public function ${ACTION_LOWER}(${ACTION}Command \$command, $ACTION \$handler): Responsable
    {
        \$result = \$handler->handle(\$command);

        return new SuccessResponse(\$result);
    }
EOF
    echo ""
fi

# Выводим информацию о маршруте
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Добавьте следующий маршрут в php/app/Api/routes.php:"
echo ""
echo "Route::post('/${FEATURE_LOWER}/${ACTION_LOWER}', [${FEATURE}Controller::class, '${ACTION_LOWER}']);"
echo ""
echo "Или для защищенного маршрута:"
echo ""
echo "Route::group(['middleware' => ['auth:sanctum']], function () {"
echo "    Route::post('/${FEATURE_LOWER}/${ACTION_LOWER}', [${FEATURE}Controller::class, '${ACTION_LOWER}']);"
echo "});"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ Структура создана успешно!"
echo ""
echo "Следующие шаги:"
echo "1. Добавьте поля в ${ACTION}Command"
echo "2. Добавьте правила валидации в ${ACTION}::validate()"
echo "3. Реализуйте бизнес-логику в ${ACTION}::handle()"
echo "4. Добавьте маршрут в routes.php"
echo "5. Добавьте use для контроллера в routes.php:"
echo "   use App\Api\Controllers\\${FEATURE}Controller;"
