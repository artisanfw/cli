<?php

namespace Artisan\Cli\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class NewCommand extends Command
{
    protected const bool IS_PACKAGIST = false;
    protected const string REPO_HUB = 'https://github.com/';
    protected const string REPO_ACCOUNT = 'artisanfw';

    protected static string $defaultName = 'new';
    protected static string $defaultDescription = 'Create a new Artisan Framework project';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription)
            ->addArgument(
                'folder',
                InputArgument::REQUIRED,
                'The folder name where the project will be created'
            )
            ->addOption(
                'version',
                null,
                InputOption::VALUE_REQUIRED,
                'Version of the starter to install (e.g. 1.0.0)'
            )
            ->addOption(
                'dev',
                'd',
                InputOption::VALUE_NONE,
                'Install latest development version (dev-main)'
            )
            ->addOption(
                'with-users',
                'u',
                InputOption::VALUE_NONE,
                'Use the Artisan package with user authentication pre-installed'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $folder      = $input->getArgument('folder');
        $version     = $input->getOption('version');
        $isDev       = $input->getOption('dev');
        $withUsers   = $input->getOption('with-users');

        [$composerCmd, $package] = $this->getComposerCommand($folder, $version, $isDev, $withUsers);

        $output->writeln('<info>Creating new project in folder:</info> ' . $folder);
        $output->writeln('<info>Using package:</info> ' . $package);

        $process = new Process($composerCmd);
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $output->writeln('<error>Project creation failed.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Project created successfully!</info>');
        return Command::SUCCESS;
    }

    private function getComposerCommand(
        string $folder,
        string $version,
        bool $isDev,
        bool $withUsers
    ): array {

        $packageName = $withUsers ? 'artisan' : 'starter';
        $package     = self::REPO_ACCOUNT . '/' . $packageName;

        $composerCmd = ['composer', 'create-project'];

        if (!self::IS_PACKAGIST) {
            $repoUrl = self::REPO_HUB . $package . '.git';
            $composerCmd[] = '--repository={"type": "vcs", "url": "' . $repoUrl . '"}';
        }

        if ($isDev || !self::IS_PACKAGIST) {
            $package .= ':dev-main';
        } elseif ($version) {
            $package .= ':' . $version;
        }

        $composerCmd[] = $package;
        $composerCmd[] = $folder;

        if ($isDev || !self::IS_PACKAGIST) {
            $composerCmd[] = '--stability=dev';
        }

        return [$composerCmd, $package];
    }
}
