<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CorsSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly string $allowedOrigin = 'http://localhost:3000')
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getMethod() === 'OPTIONS' && str_starts_with($request->getPathInfo(), '/api')) {
            $response = new Response('', Response::HTTP_NO_CONTENT);
            $this->addHeaders($response, $request->headers->get('Origin'));
            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $this->addHeaders($event->getResponse(), $event->getRequest()->headers->get('Origin'));
    }

    private function addHeaders(Response $response, ?string $origin): void
    {
        if ($origin !== $this->allowedOrigin) {
            return;
        }

        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS');
        $response->headers->set('Vary', 'Origin');
    }
}
