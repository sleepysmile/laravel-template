<?php

namespace Database\Seeders;

use App\Cli\Handlers\CreateUser;
use App\Cli\Handlers\CreateUserCommand;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $count = 20;
        $password = "12345";
        $faker = Factory::create();

        for ($userCount = 0; $userCount <= $count; $userCount++) {
            $command = new CreateUserCommand(
                $faker->email(),
                $faker->name(),
                $password
            );

            (new CreateUser())->handle($command);
        }
    }
}
