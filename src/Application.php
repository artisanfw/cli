<?php

namespace Artisan\Cli;

use Artisan\Cli\Commands\NewCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Artisan Framework CLI', '0.1.0');

        $this->add(new NewCommand());
    }
}
