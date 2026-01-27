<?php

namespace App\Http\Client\Ui\Controllers;

use App\Http\Client\Handlers\Signin\Signin;
use App\Http\Client\Handlers\Signin\SigninCommand;
use App\Http\Core\Responses\SuccessResponse;
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
