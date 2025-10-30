<?php

namespace Artisan\Cli;

use Artisan\Cli\Commands\NewCommand;
use Artisan\Cli\Commands\SelfUpdateCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public const string REPO_HUB = 'https://github.com/';
    public const string REPO_ACCOUNT = 'artisanfw';
    public const bool IS_PACKAGIST = false;

    public function __construct()
    {
        if ($this->hasLocalCli()) {
            $this->delegateToLocalCli();
            exit;
        }

        $artisan = "                _   _                 
     /\        | | (_)                
    /  \   _ __| |_ _ ___  __ _ _ __  
   / /\ \ | '__| __| / __|/ _` | '_ \ 
  / ____ \| |  | |_| \__ \ (_| | | | |
 /_/    \_\_|   \__|_|___/\__,_|_| |_|".PHP_EOL.'               Artisan Framework CLI (Global)';

        parent::__construct($artisan, '1.0.1');

        $this->add(new NewCommand());
        $this->add(new SelfUpdateCommand());
    }

    private function hasLocalCli(): bool
    {
        $localCliPath = getcwd() . DIRECTORY_SEPARATOR . 'artisan';
        return file_exists($localCliPath) && is_file($localCliPath) && is_executable($localCliPath);
    }

    private function delegateToLocalCli(): void
    {
        $localCliPath = getcwd() . DIRECTORY_SEPARATOR . 'artisan';
        passthru(PHP_BINARY . ' ' . escapeshellarg($localCliPath) . ' ' . implode(' ', array_slice($_SERVER['argv'], 1)));
    }
}
