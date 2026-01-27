<?php

namespace App\Http\Client\Queries\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FindUserQuery
{
    public function handle(FindUserQueryCommand $command)
    {
        $query = User::query()
            ->select(["id", "name", "email"]);

        if ($command->withoutCurrentUser) {
            $query->where("id", "!=", Auth::user()->id);
        }

        return $query->get();
    }
}
