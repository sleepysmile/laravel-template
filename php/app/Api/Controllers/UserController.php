<?php

namespace App\Api\Controllers;

use App\Api\Handlers\User\UpdateUser;
use App\Api\Handlers\User\UpdateUserCommand;
use App\Shared\Responses\Api\SuccessResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseClientController
{
    public function update(UpdateUserCommand $command, UpdateUser $handler)
    {
        $result = $handler->handle($command, Auth::user());

        return new SuccessResponse($result);
    }
}
