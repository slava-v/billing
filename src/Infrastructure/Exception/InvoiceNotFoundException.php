<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Exception thrown when the Invoice entity is not found.
 */
final class InvoiceNotFoundException extends AbstractUserException
{
    public function __construct(string $invoiceId, ?Throwable $previous = null)
    {
        $message = sprintf('Invoice %s not found. See logs', $invoiceId);
        parent::__construct($message, 0, $previous);
        $this
            ->setUserMessage(sprintf(
                'Company %s not found. For more details refer to documentation or contact customer support',
                $invoiceId
            ))
            ->setStatusCode(Response::HTTP_NOT_FOUND);
    }
}
