<?php

namespace Artisan\Cli\Commands;


use Artisan\Cli\Commands\Projects\BackendProject;
use Artisan\Cli\Commands\Projects\IProject;
use Artisan\Cli\Commands\Projects\SpaProject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    protected static $defaultName = 'new';
    protected static $defaultDescription = 'Create a new Artisan Framework project';

    /** @var IProject[] */
    private array $projectTypes = [];

    public function __construct()
    {
        parent::__construct();
        $this->registerProjectTypes();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('type', InputArgument::REQUIRED, 'Type of project (backend, spa, etc.)')
            ->addArgument('folder', InputArgument::REQUIRED, 'Folder name where the project will be created')
            ->addOption('ver', null, InputOption::VALUE_REQUIRED, 'Version to install (e.g. 1.0.0)')
            ->addOption('dev', 'd', InputOption::VALUE_NONE, 'Install latest dev version')
            ->addOption('with-users', 'u', InputOption::VALUE_NONE, 'Include user module (only for backend)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');
        $folder = $input->getArgument('folder');

        if (!isset($this->projectTypes[$type])) {
            $output->writeln("<error>Unknown project type: $type</error>");
            $output->writeln('Available types: ' . implode(', ', array_keys($this->projectTypes)));
            return Command::FAILURE;
        }

        $handler = $this->projectTypes[$type];
        return $handler->execute($folder, $input, $output);
    }

    private function registerProjectTypes(): void
    {
        $this->addProjectType(new BackendProject());
        $this->addProjectType(new SpaProject());
    }

    private function addProjectType(IProject $type): void
    {
        $this->projectTypes[$type->getName()] = $type;
    }
}
