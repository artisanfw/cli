<?php

namespace Artisan\Cli\Commands;

use Artisan\Cli\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;


class SelfUpdateCommand extends Command
{
    protected static $defaultName = 'self-update';
    protected static $defaultDescription = 'Update the Artisan CLI to the latest version';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currentPhar = \Phar::running(false);
        if (!$currentPhar) {
            $output->writeln('<error>This command can only be run from the packaged PHAR.</error>');
            return Command::FAILURE;
        }

        $url = Application::REPO_HUB . Application::REPO_ACCOUNT . '/cli/releases/latest/download/artisan.phar';
        $tmpFile = tempnam(sys_get_temp_dir(), 'artisan_update_');

        $output->writeln('<info>Downloading latest version...</info>');
        if (@file_put_contents($tmpFile, @file_get_contents($url)) === false) {
            $output->writeln('<error>Failed to download update from GitHub.</error>');
            return Command::FAILURE;
        }

        $fs = new Filesystem();
        $fs->chmod($tmpFile, 0755);

        $output->writeln('<info>Replacing current executable...</info>');
        try {
            $fs->rename($tmpFile, $currentPhar, true);
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to replace file. Try running with sudo:</error>');
            $output->writeln('  sudo ' . basename($_SERVER['argv'][0]) . ' self-update');
            return Command::FAILURE;
        }

        $output->writeln('<info>Artisan CLI updated successfully!</info>');
        return Command::SUCCESS;
    }
}
