<?php

namespace App\Shared\Handlers\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Data;

trait Validation
{
    protected function rules(): array
    {
        return [];
    }

    /**
     * @throws ValidationException
     */
    protected function validate(Data $command): bool
    {
        $validator = Validator::make($command->toArray(), $this->rules());
        $fails = $validator->fails();

        if ($fails) {
            throw new ValidationException($validator);
        }

        return $fails;
    }
}
