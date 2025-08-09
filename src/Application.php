<?php

namespace Artisan\Cli;

use Artisan\Cli\Commands\NewCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        $artisan = "                _   _                 
     /\        | | (_)                
    /  \   _ __| |_ _ ___  __ _ _ __  
   / /\ \ | '__| __| / __|/ _` | '_ \ 
  / ____ \| |  | |_| \__ \ (_| | | | |
 /_/    \_\_|   \__|_|___/\__,_|_| |_|".PHP_EOL.'               Artisan Framework CLI';

        parent::__construct($artisan, '0.1.0');

        $this->add(new NewCommand());
    }
}
