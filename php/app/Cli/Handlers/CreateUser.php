<?php

namespace App\Cli\Handlers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUser
{
    public function handle(CreateUserCommand $command)
    {
        $item = new User();

        $item->name = $command->name;
        $item->email = $command->email;
        $item->password = Hash::make($command->password);

        $item->save();

        return $item;
    }
}
