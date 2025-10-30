<?php

namespace Artisan\Cli\Commands\Projects;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;

class SpaProject implements IProject
{
    public function getName(): string { return 'spa'; }
    public function getDescription(): string { return 'Create a new Artisan SPA (Vue) project'; }

    public function execute(string $folder, InputInterface $input, OutputInterface $output): int
    {
        $repo = 'https://github.com/artisanfw/spa.git';

        $version = $input->getOption('ver');
        $isDev   = $input->getOption('dev');

        if ($version) {
            $branch = ltrim($version, 'v');
            $branch = 'v' . $branch;
        } elseif ($isDev) {
            $branch = 'main';
        } else {
            $branch = 'stable';
        }

        $output->writeln("<info>Cloning SPA repository from:</info> $repo ($branch)");

        //Git clone
        $process = new Process(['git', 'clone', '--depth', '1', '--branch', $branch, $repo, $folder]);
        $process->setTty(Process::isTtySupported());
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            $output->writeln("<error>Failed to clone branch or tag '$branch' from $repo.</error>");
            $output->writeln('Make sure the tag or branch exists in the repository.');
            return Command::FAILURE;
        }

        $fs = new Filesystem();
        $fs->remove($folder . '/.git');

        $output->writeln('<info>Installing npm dependencies...</info>');
        $npm = new Process(['npm', 'install'], $folder);
        $npm->setTty(Process::isTtySupported());
        $npm->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if (!$npm->isSuccessful()) {
            $output->writeln('<error>npm install failed.</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>SPA project created successfully!</info>');
        return Command::SUCCESS;
    }
}
