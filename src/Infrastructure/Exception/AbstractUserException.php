<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Exception;

use Symfony\Component\HttpFoundation\Response;

abstract class AbstractUserException extends \Exception
{
    protected string $userMessage = '';

    protected int $statusCode = Response::HTTP_BAD_REQUEST;

    final public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    final public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function setUserMessage(string $userMessage): self
    {
        $this->userMessage = $userMessage;
        return $this;
    }
}
