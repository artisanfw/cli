<?php

namespace Artisan\Cli\Commands\Projects;

use Artisan\Cli\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;

class BackendProject implements IProject
{
    public function getName(): string { return 'backend'; }
    public function getDescription(): string { return 'Create a new Artisan backend project'; }

    public function execute(string $folder, InputInterface $input, OutputInterface $output): int
    {
        $version   = $input->getOption('ver');
        $isDev     = $input->getOption('dev');
        $withUsers = $input->getOption('with-users');

        $packageName = $withUsers ? 'artisan' : 'starter';
        $package     = Application::REPO_ACCOUNT . '/' . $packageName;
        $repoUrl     = Application::REPO_HUB . $package . '.git';

        $composerCmd = ['composer', 'create-project', '--repository={"type": "vcs", "url": "' . $repoUrl . '"}'];

        if ($isDev || !Application::IS_PACKAGIST) {
            $package .= ':dev-main';
        } elseif ($version) {
            $package .= ':' . $version;
        }

        $composerCmd[] = $package;
        $composerCmd[] = $folder;

        if ($isDev || !Application::IS_PACKAGIST) {
            $composerCmd[] = '--stability=dev';
        }

        $output->writeln('<info>Creating backend project:</info> ' . $folder);
        $process = new Process($composerCmd);
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
    }
}
