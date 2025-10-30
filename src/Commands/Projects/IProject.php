<?php
namespace Artisan\Cli\Commands\Projects;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


interface IProject
{
    public function getName(): string;
    public function getDescription(): string;
    public function execute(string $folder, InputInterface $input, OutputInterface $output): int;
}
