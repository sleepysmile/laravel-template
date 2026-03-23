<?php

namespace Tests\Factory;

use App\Shared\Models\User;

class UserFactory
{
    public function faker()
    {
        return \Faker\Factory::create();
    }

    public function user(array $params = [])
    {
        $user = new User();

        $user->name = $this->faker()->uuid();
        $user->email = $this->faker()->email();
        $user->password = $this->faker()->password();

        $user->save();

        return $user;
    }
}
