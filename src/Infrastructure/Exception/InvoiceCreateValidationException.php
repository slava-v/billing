<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

final class InvoiceCreateValidationException extends AbstractUserException
{
    public function __construct(ConstraintViolationListInterface|string $errors, ?Throwable $previous = null)
    {
        if ($errors instanceof ConstraintViolationListInterface) {
            $errorMessages = array_map(
                fn(ConstraintViolationInterface $cv) => $cv->getMessage(),
                iterator_to_array($errors)
            );
        } else {
            $errorMessages = $errors;
        }

        $message = sprintf(
            'Invoice entry cannot created due to validation errors: %s',
            (is_array($errorMessages) ? implode(PHP_EOL, $errorMessages) : $errorMessages)
        );
        parent::__construct($message, 0, $previous);

        $this
            ->setUserMessage('Invoice cannot be created due to validation errors')
            ->setStatusCode(Response::HTTP_BAD_REQUEST);
    }
}
