<?php

namespace App\Infrastructure\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Logs a single "application started" event on the first
 * handled HTTP request, mainly for observability purposes.
 */
class AppStartedListener
{
    /**
     * @var bool
     */
    private bool $logged = false;

    public function __construct(
        private LoggerInterface $poesiLogger
    ) {
    }

    /**
     * This listener is invoked on each request, but we only log
     * once per process to avoid flooding the logs.
     *
     * @param RequestEvent $event
     */
    public function __invoke(RequestEvent $event): void
    {
        // Only log for the main (master) request
        if ($this->logged || !$event->isMainRequest()) {
            return;
        }

        $this->logged = true;

        // Try to resolve the current environment from env variables.
        $environment = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'unknown';

        $request = $event->getRequest();

        $this->poesiLogger->info('POESI API kernel started.', [
            'timestamp'   => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            'environment' => $environment,
            'method'      => $request->getMethod(),
            'path'        => $request->getPathInfo(),
        ]);
    }
}
