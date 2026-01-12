<?php

namespace App\Infrastructure\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Adds CORS headers for local development origins and handles OPTIONS preflight.
 */
final class CorsSubscriber implements EventSubscriberInterface
{
    private const ALLOWED_METHODS = 'GET, POST, PUT, PATCH, DELETE, OPTIONS';
    private const ALLOWED_HEADERS = 'Authorization, Content-Type, Accept, Origin, X-Requested-With';

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isApiRequest($request->getPathInfo())) {
            return;
        }

        if ($request->getMethod() !== 'OPTIONS') {
            return;
        }

        $origin = $request->headers->get('Origin');
        if (!$this->isAllowedOrigin($origin)) {
            return;
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        $this->applyCorsHeaders($response, $origin);
        $event->setResponse($response);
        $event->stopPropagation();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isApiRequest($request->getPathInfo())) {
            return;
        }

        $origin = $request->headers->get('Origin');
        if (!$this->isAllowedOrigin($origin)) {
            return;
        }

        $this->applyCorsHeaders($event->getResponse(), $origin);
    }

    private function isApiRequest(string $path): bool
    {
        return str_starts_with($path, '/api');
    }

    private function isAllowedOrigin(?string $origin): bool
    {
        if ($origin === null || $origin === '') {
            return false;
        }

        $host = parse_url($origin, PHP_URL_HOST);
        if (!is_string($host) || $host === '') {
            return false;
        }

        $allowedHosts = ['localhost', '127.0.0.1', '::1'];
        if (in_array($host, $allowedHosts, true)) {
            return true;
        }

        return preg_match('/^(10|192\\.168|172\\.(1[6-9]|2\\d|3[0-1]))\\./', $host) === 1;
    }

    private function applyCorsHeaders(Response $response, string $origin): void
    {
        $headers = $response->headers;
        $headers->set('Access-Control-Allow-Origin', $origin);
        $headers->set('Access-Control-Allow-Methods', self::ALLOWED_METHODS);
        $headers->set('Access-Control-Allow-Headers', self::ALLOWED_HEADERS);
        $headers->set('Access-Control-Max-Age', '600');
        $headers->set('Vary', 'Origin');
    }
}
