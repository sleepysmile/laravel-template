<?php

namespace App\Http\Client\Handlers\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Query\Builder;

class CreateOrGetChat
{
    public function handle(CreateOrGetChatCommand $command, User $user): Chat
    {
        $firstUserId = $user->id;
        $secondUserId = $command->user_id;

        /** @var Chat|null $chat */
        $chat = Chat::query()
            ->where(function ($where) use ($firstUserId, $secondUserId) {
                /** @var Builder $where */
                $where->where("first_user_id", $firstUserId)
                    ->where("second_user_id", $secondUserId);
            })
            ->orWhere(function ($where) use ($firstUserId, $secondUserId) {
                /** @var Builder $where */
                $where->where("first_user_id", $secondUserId)
                    ->where("second_user_id", $firstUserId);
            })
            ->first();

        if ($chat === null) {
            $chat = new Chat();

            $chat->first_user_id = $firstUserId;
            $chat->second_user_id = $secondUserId;

            $chat->save();
        }

        return $chat;
    }
}
