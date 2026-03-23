<?php

namespace App\Shared\Services\Filesystem\FilePathStrategies;

use App\Shared\Services\Filesystem\Contracts\FilePathStrategy;
use Illuminate\Database\Eloquent\Model;

class ModelStrategy implements FilePathStrategy
{
    protected Model $model;
    protected string $fieldName;

    public function __construct(Model $model, string $fieldName)
    {
        $this->model = $model;
        $this->fieldName = $fieldName;
    }

    public function path(): string
    {
        return $this->model->getTable()
            . DIRECTORY_SEPARATOR
            . $this->model->getKey()
            . DIRECTORY_SEPARATOR
            . $this->fieldName
            . DIRECTORY_SEPARATOR;
    }
}
