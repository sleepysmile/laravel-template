<?php

declare(strict_types=1);

namespace App\Cli\Ui;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $name = "x:test";

    public function handle() {}
}
