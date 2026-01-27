<?php

namespace App\Http\Client\Queries\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use LogicException;

class FindMessageQuery
{
    public function handle(FindMessageQueryCommand $command, User $user): LengthAwarePaginator
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

        return Message::query()
            ->where("chat_id", $chat->id)
            ->orderBy("id", $order)
            ->paginate(
                perPage: $command->per_page,
                page: $command->page
            );
    }
}

