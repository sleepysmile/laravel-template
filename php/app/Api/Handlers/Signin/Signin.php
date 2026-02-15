<?php

namespace App\Api\Handlers\Signin;

use App\Shared\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use LogicException;

class Signin
{
    protected function validate(SigninCommand $command)
    {
        $validator = Validator::make($command->toArray(), [
            "email" => [
                "required",
                "email"
            ],
            "password" => [
                "required",
                "min:3"
            ]
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function handle(SigninCommand $command): string
    {
        $this->validate($command);

        /** @var User|null $user */
        $user = User::query()
            ->where("email", $command->email)
            ->first();

        if ($user === null) {
            throw new LogicException("такой пользователь не зарегестрирован");
        }

        $checkPassword = Hash::check($command->password, $user->password);

        if (! $checkPassword) {
            throw new LogicException("неверный пароль");
        }

        return $user->createToken("clientAuthToken")->plainTextToken;
    }
}
