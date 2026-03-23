<?php

namespace App\Api\Handlers\User;

use App\Shared\Models\User;
use App\Shared\Services\Filesystem\FilePathStrategies\StaticExportStrategy;
use App\Shared\Services\Filesystem\ResourceFilesystem;
use App\Shared\Services\Filesystem\StaticFilesystem;

class ExportUser
{
    protected ResourceFilesystem $filesystem;

    public function __construct(ResourceFilesystem $filesystem)
    {
        $this->filesystem = $filesystem
            ->setPathStrategy(new StaticExportStrategy());
    }

    public function handle(ExportUserCommand $command)
    {
        $fs = $this->filesystem;
        // todo how to get path
        $resource = $fs->open("csv");

        User::query()
            ->select($command->fields)
            ->chunk(100, function ($userChuck) use ($resource, $fs) {
                foreach ($userChuck as $user) {
                    $fs->put($resource, [
                        $user->name,
                        $user->email
                    ]);
                }
            });

        return $fs->close($resource);
    }
}
