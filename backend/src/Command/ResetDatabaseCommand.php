<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:reset-db',
    description: 'Drop/create database, run migrations, and seed data.'
)]
final class ResetDatabaseCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'no-confirm',
            null,
            InputOption::VALUE_NONE,
            'Skip confirmation prompt (default behavior).'
        );
        $this->addOption(
            'no-seed',
            null,
            InputOption::VALUE_NONE,
            'Skip seeding after migrations.'
        );
        $this->addOption(
            'insert-only',
            null,
            InputOption::VALUE_NONE,
            'Insert-only mode when seeding (no updates).'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln('');
        $output->writeln('<info>=== doctrine:database:drop ===</info>');
        $dropCode = $this->dropDatabase($output);
        if ($dropCode !== Command::SUCCESS) {
            return $dropCode;
        }

        $steps = [
            ['doctrine:database:create', []],
        ];

        if ($this->hasMigrations()) {
            $steps[] = ['doctrine:migrations:migrate', ['--no-interaction' => true]];
        } else {
            $output->writeln('<comment>No migrations found: using doctrine:schema:create.</comment>');
            $steps[] = ['doctrine:schema:create', []];
        }

        $skipSeed = (bool) $input->getOption('no-seed');
        if (!$skipSeed) {
            $seedOptions = ['--no-migrate' => true];
            if ((bool) $input->getOption('insert-only')) {
                $seedOptions['--insert-only'] = true;
            }
            $steps[] = ['app:seed-all', $seedOptions];
        }

        foreach ($steps as [$name, $options]) {
            $output->writeln('');
            $output->writeln(sprintf('<info>=== %s ===</info>', $name));
            $code = $this->runSubCommand($name, $options, $output);
            if ($code !== Command::SUCCESS) {
                $output->writeln(sprintf('<error>Stopped: %s failed.</error>', $name));
                return $code;
            }

            if ($name === 'doctrine:database:create') {
                // Ensure the next command reconnects with a fresh connection.
                $this->connection->close();
            }
        }

        $output->writeln('');
        $output->writeln('<info>Database reset completed successfully.</info>');

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

    private function hasMigrations(): bool
    {
        $path = $this->projectDir . DIRECTORY_SEPARATOR . 'migrations';
        if (!is_dir($path)) {
            return false;
        }

        $files = glob($path . DIRECTORY_SEPARATOR . '*.php');
        if ($files === false) {
            return false;
        }

        return $files !== [];
    }

    private function dropDatabase(OutputInterface $output): int
    {
        $params = $this->connection->getParams();
        $driver = (string) ($params['driver'] ?? '');
        $dbName = $this->connection->getDatabase();

        if ($dbName === null || $dbName === '') {
            $output->writeln('<error>Database name not configured.</error>');
            return Command::FAILURE;
        }

        if ($driver !== 'pdo_pgsql') {
            return $this->runSubCommand(
                'doctrine:database:drop',
                ['--force' => true, '--if-exists' => true],
                $output
            );
        }

        $maintenanceDb = $dbName === 'postgres' ? 'template1' : 'postgres';
        $maintenanceParams = $params;
        $maintenanceParams['dbname'] = $maintenanceDb;
        unset($maintenanceParams['url']);

        $maintenanceConnection = DriverManager::getConnection($maintenanceParams);

        try {
            $maintenanceConnection->executeStatement(
                'SELECT pg_terminate_backend(pid)
                 FROM pg_stat_activity
                 WHERE datname = :dbname AND pid <> pg_backend_pid()',
                ['dbname' => $dbName]
            );

            $quotedDb = $maintenanceConnection->getDatabasePlatform()->quoteIdentifier($dbName);
            $maintenanceConnection->executeStatement(sprintf('DROP DATABASE IF EXISTS %s', $quotedDb));
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>Could not drop database "%s".</error>', $dbName));
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return Command::FAILURE;
        } finally {
            $maintenanceConnection->close();
        }

        $output->writeln(sprintf('<info>Dropped database "%s".</info>', $dbName));

        return Command::SUCCESS;
    }
}
