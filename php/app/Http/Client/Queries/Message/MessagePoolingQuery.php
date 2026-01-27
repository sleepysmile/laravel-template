<?php

namespace App\Http\Client\Queries\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use LogicException;

class MessagePoolingQuery
{
    public function handle(MessagePoolingQueryCommand $command, User $user): Collection
    {
        /** @var Chat|null $chat */
        $chat = Chat::query()->find($command->chat_id);
        if ($chat === null) {
            throw new LogicException("Чат не найден");
        }

        $isParticipant = $chat->first_user_id === $user->id || $chat->second_user_id === $user->id;
        if (! $isParticipant) {
            throw new LogicException("Пользователь не является участником чата");
        }

        $order = strtolower($command->order) === 'desc' ? 'desc' : 'asc';

        $query = Message::query()
            ->where("chat_id", $chat->id);

        if ($command->last_id !== null) {
            $query->where("id", ">", $command->last_id);
        }

        return $query
            ->orderBy("id", $order)
            ->get();
    }
}

