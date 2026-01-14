<?php

namespace App\Command\Lore;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:lore:sync-all',
    description: 'Run all lore sync commands (feathers, moods, symbols, relics, totems).'
)]
final class SyncLoreAllCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Run all sync commands in dry-run mode.')
            ->addOption('insert-only', null, InputOption::VALUE_NONE, 'Run all sync commands with insert-only mode.')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Run all sync commands with purge mode.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = (bool) $input->getOption('dry-run');
        $insertOnly = (bool) $input->getOption('insert-only');
        $purge = (bool) $input->getOption('purge');

        $commands = [
            'app:lore:sync-feathers',
            'app:lore:sync-moods',
            'app:lore:sync-symbols',
            'app:lore:sync-relics',
            'app:lore:sync-totems',
        ];

        foreach ($commands as $name) {
            $output->writeln('');
            $output->writeln(sprintf('<info>=== %s ===</info>', $name));

            $args = [];
            if ($dryRun) {
                $args['--dry-run'] = true;
            }
            if ($insertOnly) {
                $args['--insert-only'] = true;
            }
            if ($purge) {
                $args['--purge'] = true;
            }

            $code = $this->runSubCommand($name, $args, $output);
            if ($code !== Command::SUCCESS) {
                $output->writeln(sprintf('<error>Stopped: %s failed.</error>', $name));
                return $code;
            }
        }

        $output->writeln('');
        $output->writeln('<info>All lore references synced successfully.</info>');

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
