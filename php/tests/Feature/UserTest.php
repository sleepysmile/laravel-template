<?php

namespace Tests\Feature;

use App\Console\Handlers\CreateUser;
use App\Console\Handlers\CreateUserCommand;
use App\Shared\Models\User;
use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_update()
    {
        $email = "test@test.ru";
        $faker = Factory::create();

        /** @var User|null $user */
        $user = User::query()
            ->where("email", $email)
            ->first();

        if ($user === null) {
            $createUserCommand = CreateUserCommand::from([
                "name" => $faker->name,
                "email" => $email,
                "password" => "12345"
            ]);
            $user = (new CreateUser())
                ->handle($createUserCommand);
        }

        $payload = [
            "name" => $faker->name,
            "email" => $email,
            "avatar" => UploadedFile::fake()->image("test.jpg"),
        ];

        Sanctum::actingAs($user);
        $response = $this->postJson("/api/user", $payload);

        $response->assertOk();
    }
}
