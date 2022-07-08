<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Events\EventSubscriber;

use Billing\Infrastructure\Exception\AbstractUserException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function entityNotFound(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof AbstractUserException) {
            /** $error message can also have client message */
            $event->setResponse(new JsonResponse(
                [
                    'success' => false,
                    'error' => $event->getThrowable()->getUserMessage(),
                ],
                $event->getThrowable()->getStatusCode()
            ));
        } else {
            $event->setResponse(new JsonResponse(
                [
                    'success' => false,
                    'error' => 'Please see documentation or contact "support@Billing-yes.com" for support',
                ],
                Response::HTTP_BAD_REQUEST
            ));
        }
    }

    public function logException(ExceptionEvent $event): void
    {
        $this->logger->error($event->getThrowable()->getMessage(), [$event->getThrowable()->getTraceAsString()]);
    }

    /** @inheritdoc */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['entityNotFound', 10],
                ['logException', 0],
            ],
        ];
    }
}
