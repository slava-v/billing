<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Exception;

use Billing\DataLayer\Entity\Company;
use Billing\DataLayer\Entity\Invoice;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DebtorLimitExceededException extends AbstractUserException
{
    public function __construct(Invoice $invoice, Company $creditor, int $amount, ?Throwable $previous = null)
    {
        $userMessage = sprintf(
            'Invoice rejected. Reason: Invoice "%s" for "%s" exceeds limit by %d Eur',
            $invoice->getIdAsString(),
            $creditor->getIdAsString(),
            $amount
        );

        parent::__construct($userMessage, 0, $previous);

        $this
            ->setUserMessage($userMessage)
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }
}
