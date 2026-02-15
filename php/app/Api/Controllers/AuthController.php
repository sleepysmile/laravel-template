<?php

namespace App\Api\Controllers;

use App\Api\Handlers\Signin\Signin;
use App\Api\Handlers\Signin\SigninCommand;
use App\Shared\Responses\Api\SuccessResponse;
use Illuminate\Contracts\Support\Responsable;

class AuthController extends BaseClientController
{
    public function signin(SigninCommand $command, Signin $handler): Responsable
    {
        $token = $handler->handle($command);

        return new SuccessResponse([
            "token" => $token
        ]);
    }
}
