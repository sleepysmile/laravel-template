<?php

namespace App\Http\Client\Ui\Controllers;

use App\Http\Client\Handlers\Chat\CreateOrGetChat;
use App\Http\Client\Handlers\Chat\CreateOrGetChatCommand;
use App\Http\Core\Responses\SuccessResponse;
use Illuminate\Http\Request;

class ChatController extends BaseClientController
{
    public function init(Request $request, CreateOrGetChat $handler)
    {
        $command = CreateOrGetChatCommand::from($request);
        $user = $request->user();

        $results = $handler->handle($command, $user);

        return new SuccessResponse($results);
    }
}
