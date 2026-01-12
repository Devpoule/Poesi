<?php

namespace App\Infrastructure\EventListener;

use App\Domain\Entity\User;
use App\Http\Response\ApiResponseFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Bundle\SecurityBundle\Security;

final class ApiRateLimiterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'limiter.api')]
        private RateLimiterFactory $apiLimiter,
        private Security $security
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        if ($request->getMethod() === 'OPTIONS') {
            return;
        }

        $path = $request->getPathInfo();
        if ($path === '/api/login_check' || $path === '/api/token/refresh') {
            return;
        }

        $user = $this->security->getUser();
        $key = 'anon:' . ($request->getClientIp() ?? 'unknown');
        if ($user instanceof User) {
            $key = 'user:' . (string) $user->getId();
        }

        $limit = $this->apiLimiter->create($key)->consume(1);
        if (!$limit->isAccepted()) {
            $event->setResponse(
                ApiResponseFactory::error(
                    message: 'Rate limit exceeded.',
                    code: 'RATE_LIMITED',
                    type: 'warning',
                    errors: null,
                    data: null,
                    httpStatus: 429
                )
            );
        }
    }
}
