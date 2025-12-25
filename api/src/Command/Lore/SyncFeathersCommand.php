<?php

namespace App\Command\Lore;

use App\Domain\Entity\Feather;
use App\Infrastructure\Repository\FeatherRepository;
use JsonException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:lore:sync-feathers',
    description: 'Sync Feather reference data from resources/lore/feathers.initial.json (upsert by key).'
)]
final class SyncFeathersCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly FeatherRepository $featherRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not write anything to database.')
            ->addOption('insert-only', null, InputOption::VALUE_NONE, 'Only insert missing keys, never update existing rows.')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Delete DB rows that are not present in JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = (bool) $input->getOption('dry-run');
        $insertOnly = (bool) $input->getOption('insert-only');
        $purge = (bool) $input->getOption('purge');

        $path = rtrim($this->kernel->getProjectDir(), DIRECTORY_SEPARATOR) . '/resources/lore/feathers.initial.json';

        if (!is_file($path)) {
            $output->writeln(sprintf('<error>File not found:</error> %s', $path));
            return Command::FAILURE;
        }

        try {
            $rows = $this->readJson($path);
        } catch (\RuntimeException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $em = $this->featherRepository->getEntityManager();
        $keysFromJson = [];

        foreach ($rows as $i => $row) {
            $key = $this->requireString($row, 'key', $i);
            $label = $this->requireString($row, 'label', $i);
            $description = isset($row['description']) ? (string) $row['description'] : null;
            $icon = isset($row['icon']) ? (string) $row['icon'] : null;

            $keysFromJson[] = $key;

            /** @var Feather|null $feather */
            $feather = $this->featherRepository->findOneBy(['key' => $key]);

            if ($feather === null) {
                $feather = new Feather();
                $feather->setKey($key);
                $em->persist($feather);
                $output->writeln(sprintf('➕ insert: %s (%s)', $label, $key));
            } else {
                if ($insertOnly) {
                    $output->writeln(sprintf('⏭️  skip: %s (%s) (insert-only)', $label, $key));
                    continue;
                }
                $output->writeln(sprintf('♻️  update: %s (%s)', $label, $key));
            }

            $feather->setLabel($label);
            $feather->setDescription($description);
            $feather->setIcon($icon);
        }

        if ($purge) {
            $existing = $this->featherRepository->findAll();
            foreach ($existing as $feather) {
                if (!in_array($feather->getKey(), $keysFromJson, true)) {
                    $output->writeln(sprintf('🗑️  purge: %s (%s)', $feather->getLabel(), $feather->getKey()));
                    if (!$dryRun) {
                        $em->remove($feather);
                    }
                }
            }
        }

        if ($dryRun) {
            $output->writeln('<comment>Dry-run enabled: no changes were written.</comment>');
            return Command::SUCCESS;
        }

        $em->flush();
        $output->writeln('<info>Feathers synced successfully.</info>');

        return Command::SUCCESS;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function readJson(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \RuntimeException(sprintf('Unable to read file: %s', $path));
        }

        try {
            /** @var mixed $data */
            $data = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException(sprintf('Invalid JSON: %s (%s)', $path, $e->getMessage()));
        }

        if (!is_array($data)) {
            throw new \RuntimeException(sprintf('Invalid JSON structure (expected list): %s', $path));
        }

        /** @var list<array<string, mixed>> $data */
        return $data;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function requireString(array $row, string $field, int $index): string
    {
        if (!isset($row[$field]) || trim((string) $row[$field]) === '') {
            throw new \RuntimeException(sprintf('Missing "%s" at row #%d', $field, $index));
        }

        return (string) $row[$field];
    }
}
