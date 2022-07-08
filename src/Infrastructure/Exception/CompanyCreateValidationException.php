<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class CompanyCreateValidationException extends AbstractUserException
{
    public function __construct(ConstraintViolationListInterface $errors, ?Throwable $previous = null)
    {
        $errorMessages = array_map(
            fn(ConstraintViolationInterface $cv) => $cv->getMessage(),
            iterator_to_array($errors)
        );

        $message = sprintf(
            'Company entry cannot created due to validation errors: %s',
            implode(PHP_EOL, $errorMessages)
        );
        parent::__construct($message, 0, $previous);

        $this
            ->setUserMessage('Company cannot be created due to validation errors')
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }
}
