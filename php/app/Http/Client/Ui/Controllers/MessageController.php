<?php

namespace App\Http\Client\Ui\Controllers;

use App\Http\Client\Handlers\Message\CreateMessage;
use App\Http\Client\Handlers\Message\CreateMessageCommand;
use App\Http\Client\Queries\Message\FindMessageQuery;
use App\Http\Client\Queries\Message\FindMessageQueryCommand;
use App\Http\Client\Queries\Message\MessagePoolingQuery;
use App\Http\Client\Queries\Message\MessagePoolingQueryCommand;
use App\Http\Client\Ui\Responses\FindMessageResponse;
use App\Http\Core\Responses\SuccessResponse;
use Illuminate\Http\Request;

class MessageController extends BaseClientController
{
    public function find(Request $request, FindMessageQuery $query)
    {
        $command = FindMessageQueryCommand::from($request);
        $user = $request->user();
        $results = $query->handle($command, $user);

        return FindMessageResponse::collection($results);
    }

    public function pooling(Request $request, MessagePoolingQuery $query)
    {
        $command = MessagePoolingQueryCommand::from($request);
        $user = $request->user();

        $results = $query->handle($command, $user);

        return FindMessageResponse::collection($results);
    }

    public function create(Request $request, CreateMessage $handler)
    {
        $command = CreateMessageCommand::from($request);
        $sender = $request->user();

        $message = $handler->handle($command, $sender);

        return new SuccessResponse($message);
    }
}
