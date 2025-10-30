<?php
namespace Artisan\Cli\Commands\Projects;

use Artisan\Cli\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;

class SpaProject implements IProject
{
    public function getName(): string { return 'spa'; }
    public function getDescription(): string { return 'Create a new Artisan SPA project (Vue)'; }

    public function execute(string $folder, InputInterface $input, OutputInterface $output): int
    {
        $version = $input->getOption('ver');
        $isDev   = $input->getOption('dev');

        $package = Application::REPO_ACCOUNT . '/spa';
        $repoUrl = Application::REPO_HUB . $package . '.git';

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

        $output->writeln('<info>Creating SPA project:</info> ' . $folder);
        $process = new Process($composerCmd);
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
    }
}
