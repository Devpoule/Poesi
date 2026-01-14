<?php

namespace App\Infrastructure\EventListener;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

final class LoginAttemptSubscriber implements EventSubscriberInterface
{
    private const MAX_ATTEMPTS = 5;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure',
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $email = $this->extractEmail($event->getRequest()->getContent());
        if ($email === null) {
            return;
        }

        $user = $this->userRepository->findOneByEmail($email);
        if (!$user instanceof User) {
            return;
        }

        $user->incrementFailedLoginAttempts();
        if ($user->getFailedLoginAttempts() >= self::MAX_ATTEMPTS) {
            $user->lock();
        }

        $this->userRepository->save($user);
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $user->resetFailedLoginAttempts();
        $this->userRepository->save($user);
    }

    private function extractEmail(string $content): ?string
    {
        if ($content === '') {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return null;
        }

        $email = $data['email'] ?? null;
        if (!is_string($email) || trim($email) === '') {
            return null;
        }

        return trim($email);
    }
}
