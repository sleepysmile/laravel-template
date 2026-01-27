<?php

namespace App\Http\Client\Ui\Controllers;

use App\Http\Client\Queries\User\FindUserQuery;
use App\Http\Client\Queries\User\FindUserQueryCommand;
use App\Http\Client\Ui\Responses\FindUserResponse;

class UserController extends BaseClientController
{
    public function find(FindUserQuery $query)
    {
        $command = new FindUserQueryCommand(true);
        $results = $query->handle($command);

        return FindUserResponse::collection($results);
    }
}
