<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Exception thrown when the company entity is not found.
 */
final class CompanyNotFoundException extends AbstractUserException
{
    public function __construct(string $companyIdOrName, ?Throwable $previous = null)
    {
        $message = sprintf('Company %s not found. See logs', $companyIdOrName);
        parent::__construct($message, 0, $previous);
        $this
            ->setUserMessage(sprintf(
                'Company %s not found. For more details refer to documentation or contact customer support',
                $companyIdOrName
            ))
            ->setStatusCode(Response::HTTP_NOT_FOUND);
    }
}
