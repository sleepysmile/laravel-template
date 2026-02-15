<?php

namespace App\Console\Commands;

use App\Console\Handlers\CreateUser;
use App\Console\Handlers\CreateUserCommand;
use Illuminate\Console\Command;
use Throwable;

class UserMakeCli extends Command
{
    protected $name = "x:user:make";

    public function handle()
    {
        $name = $this->ask("name:");
        $email = $this->ask("email:");
        $password = $this->ask("password:");

        $command = new CreateUserCommand(
            $email,
            $name,
            $password
        );

        try {
            (new CreateUser())
                ->handle($command);

            $this->info("Success create user");

            return Command::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());


            return Command::FAILURE;
        }
    }
}
