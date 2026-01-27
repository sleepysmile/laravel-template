<?php

namespace App\Http\Client\Handlers\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use LogicException;

class CreateMessage
{
    protected function validateInput(CreateMessageCommand $command): void
    {
        $validator = Validator::make($command->toArray(), [
            "chat_id" => ["required", "integer", "min:1"],
            "body" => ["required", "string", "min:1", "max:10000"],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    protected function assertUserInChat(Chat $chat, User $user): void
    {
        $isParticipant = $chat->first_user_id === $user->id || $chat->second_user_id === $user->id;
        if (! $isParticipant) {
            throw new LogicException("Пользователь не является участником чата");
        }
    }

    public function handle(CreateMessageCommand $command, User $sender): Message
    {
        $this->validateInput($command);

        /** @var Chat|null $chat */
        $chat = Chat::query()->find($command->chat_id);
        if ($chat === null) {
            throw new LogicException("Чат не найден");
        }

        $this->assertUserInChat($chat, $sender);

        $message = new Message();
        $message->chat_id = $chat->id;
        $message->sender_id = $sender->id;
        $message->body = $command->body;
        $message->save();

        return $message;
    }
}

