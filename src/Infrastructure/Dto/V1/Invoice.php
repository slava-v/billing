<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Dto\V1;

use Billing\DataLayer\Enums\InvoiceStatus;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class Invoice
{
    private Uuid $id;

    private Uuid $debtor;

    private Uuid $creditor;

    #[Assert\GreaterThan(0)]
    private int $total;

    #[Assert\Choice(
        callback: [InvoiceStatus::class, 'casesValues'],
        message: 'The value {{ value }} you selected is not a valid choice for "Status" field'
    )]
    private string $status;

    private \DateTimeImmutable $paidAt;

    private \DateTimeImmutable $createdAt;

    private \DateTimeImmutable $updatedAt;

    private string $source;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getDebtor(): Uuid
    {
        return $this->debtor;
    }

    public function setDebtor(Uuid $debtorId): self
    {
        $this->debtor = $debtorId;
        return $this;
    }

    public function getCreditor(): Uuid
    {
        return $this->creditor;
    }

    public function setCreditor(Uuid $creditorId): self
    {
        $this->creditor = $creditorId;
        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getPaidAt(): \DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }
}
