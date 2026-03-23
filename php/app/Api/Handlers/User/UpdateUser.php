<?php

namespace App\Api\Handlers\User;

use App\Shared\Handlers\Traits\Validation;
use App\Shared\Models\User;
use App\Shared\Services\Filesystem\Adapters\FileAdapter;
use App\Shared\Services\Filesystem\ModelFilesystem;

class UpdateUser
{
    use Validation;

    protected function rules(): array
    {
        return [
            "avatar" => [
                "required",
                "image",
                "max:500"
            ]
        ];
    }

    protected ModelFilesystem $filesystem;

    public function __construct(ModelFilesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function handle(UpdateUserCommand $command, User $user)
    {
        $this->validate($command);

        $user->name = $command->name;
        $user->email = $command->email;

        $user->save();

        $user->avatar = $this->filesystem
            ->store($user, "avatar", FileAdapter::fromUploadedFile($command->avatar));

        $user->save();

        return $user;
    }
}
