<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:seed-all',
    description: 'Run migrations, sync lore, and seed users in one command.'
)]
final class SeedAllCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run sub-commands in dry-run mode.')
            ->addOption('insert-only', null, InputOption::VALUE_NONE, 'Insert only (lore + users).')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Purge existing lore before inserting.')
            ->addOption('no-migrate', null, InputOption::VALUE_NONE, 'Skip doctrine migrations.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = (bool) $input->getOption('dry-run');
        $insertOnly = (bool) $input->getOption('insert-only');
        $purge = (bool) $input->getOption('purge');
        $skipMigrate = (bool) $input->getOption('no-migrate');

        $steps = [];

        if (!$skipMigrate) {
            $migrateOptions = ['--no-interaction' => true];
            if ($dryRun) {
                $migrateOptions['--dry-run'] = true;
            }
            $steps[] = ['doctrine:migrations:migrate', $migrateOptions];
        }

        $loreOptions = [];
        if ($dryRun) {
            $loreOptions['--dry-run'] = true;
        }
        if ($insertOnly) {
            $loreOptions['--insert-only'] = true;
        }
        if ($purge) {
            $loreOptions['--purge'] = true;
        }
        $steps[] = ['app:lore:sync-all', $loreOptions];

        $seedOptions = [];
        if ($dryRun) {
            $seedOptions['--dry-run'] = true;
        }
        if ($insertOnly) {
            $seedOptions['--insert-only'] = true;
        }
        $steps[] = ['app:seed-users', $seedOptions];

        foreach ($steps as [$name, $options]) {
            $output->writeln('');
            $output->writeln(sprintf('<info>=== %s ===</info>', $name));
            $code = $this->runSubCommand($name, $options, $output);
            if ($code !== Command::SUCCESS) {
                $output->writeln(sprintf('<error>Stopped: %s failed.</error>', $name));
                return $code;
            }
        }

        $output->writeln('');
        $output->writeln('<info>Seed completed successfully.</info>');

        return Command::SUCCESS;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function runSubCommand(string $commandName, array $options, OutputInterface $output): int
    {
        $command = $this->getApplication()?->find($commandName);

        if ($command === null) {
            $output->writeln(sprintf('<error>Command not found:</error> %s', $commandName));
            return Command::FAILURE;
        }

        $input = new ArrayInput(array_merge(['command' => $commandName], $options));
        $input->setInteractive(false);

        return $command->run($input, $output);
    }
}
