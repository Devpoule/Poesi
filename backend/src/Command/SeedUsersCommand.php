<?php

namespace App\Command;

use App\Domain\Entity\User;
use App\Domain\Entity\Totem;
use App\Domain\Enum\MoodColor;
use App\Infrastructure\Repository\TotemRepository;
use App\Infrastructure\Repository\UserRepository;
use JsonException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:seed-users',
    description: 'Seed users from resources/users.initial.json (upsert by email).'
)]
final class SeedUsersCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly UserRepository $userRepository,
        private readonly TotemRepository $totemRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not write anything to database.')
            ->addOption('insert-only', null, InputOption::VALUE_NONE, 'Only insert missing emails, never update existing rows.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dryRun = (bool) $input->getOption('dry-run');
        $insertOnly = (bool) $input->getOption('insert-only');

        $path = rtrim($this->kernel->getProjectDir(), DIRECTORY_SEPARATOR) . '/resources/users.initial.json';

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

        $em = $this->userRepository->getEntityManager();

        foreach ($rows as $i => $row) {
            $email = $this->requireString($row, 'email', $i);
            $passwordHash = $this->readPasswordHash($row, $i);

            $roles = $this->readRoles($row);
            $pseudo = isset($row['pseudo']) ? trim((string) $row['pseudo']) : null;
            $moodColor = $this->readMoodColor($row, $i);
            $totem = $this->readTotem($row, $i);

            /** @var User|null $user */
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if ($user === null) {
                $user = new User();
                $user->setEmail($email);
                $em->persist($user);
                $output->writeln(sprintf('insert: %s', $email));
            } else {
                if ($insertOnly) {
                    $output->writeln(sprintf('skip: %s (insert-only)', $email));
                    continue;
                }
                $output->writeln(sprintf('update: %s', $email));
            }

            $user->setPassword($passwordHash);
            $user->setRoles($roles);

            if ($pseudo !== null && $pseudo !== '') {
                $user->setPseudo($pseudo);
            }

            if ($moodColor !== null) {
                $user->setMoodColor($moodColor);
            }

            if ($totem !== null) {
                $user->setTotem($totem);
            }
        }

        if ($dryRun) {
            $output->writeln('<comment>Dry-run enabled: no changes were written.</comment>');
            return Command::SUCCESS;
        }

        $em->flush();
        $output->writeln('<info>Users seeded successfully.</info>');

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

    /**
     * @param array<string, mixed> $row
     */
    private function readPasswordHash(array $row, int $index): string
    {
        if (isset($row['passwordHash']) && trim((string) $row['passwordHash']) !== '') {
            return (string) $row['passwordHash'];
        }

        if (isset($row['password']) && trim((string) $row['password']) !== '') {
            return password_hash((string) $row['password'], PASSWORD_BCRYPT);
        }

        throw new \RuntimeException(sprintf('Missing "passwordHash" or "password" at row #%d', $index));
    }

    /**
     * @param array<string, mixed> $row
     * @return string[]
     */
    private function readRoles(array $row): array
    {
        if (!isset($row['roles']) || !is_array($row['roles'])) {
            return ['ROLE_USER'];
        }

        $roles = array_values(array_filter(
            $row['roles'],
            static fn ($r) => is_string($r) && trim($r) !== ''
        ));

        return $roles !== [] ? $roles : ['ROLE_USER'];
    }

    /**
     * @param array<string, mixed> $row
     */
    private function readMoodColor(array $row, int $index): ?MoodColor
    {
        if (!isset($row['moodColor']) || trim((string) $row['moodColor']) === '') {
            return null;
        }

        try {
            return MoodColor::from((string) $row['moodColor']);
        } catch (\ValueError) {
            throw new \RuntimeException(sprintf('Invalid moodColor at row #%d', $index));
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function readTotem(array $row, int $index): ?Totem
    {
        if (isset($row['totemId'])) {
            $totemId = (int) $row['totemId'];
            $totem = $this->totemRepository->getById($totemId);
            if ($totem === null) {
                throw new \RuntimeException(sprintf('Unknown totemId at row #%d', $index));
            }
            return $totem;
        }

        if (isset($row['totemKey']) && trim((string) $row['totemKey']) !== '') {
            $totem = $this->totemRepository->getByKey((string) $row['totemKey']);
            if ($totem === null) {
                throw new \RuntimeException(sprintf('Unknown totemKey at row #%d', $index));
            }
            return $totem;
        }

        return null;
    }
}
