<?php

declare(strict_types=1);

namespace Billing\DataLayer\Entity;

use Billing\DataLayer\Enums\InvoiceStatus;
use Billing\DataLayer\Repository\InvoiceRepositoryInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InvoiceRepositoryInterface::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(Company::class)]
    private Company $debtor;

    #[ORM\ManyToOne(Company::class)]
    private Company $creditor;

    #[ORM\Column(type: 'integer')]
    private int $total;

    /**
     * @todo add doctrine enum type, and validation of field values using this enum type
     */
    #[ORM\Column(type: 'string', length: 20, options: ['default' => InvoiceStatus::NEW])]
    private string $status;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $paidAt;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private string $source;

    /**
     * For auditing purposes, user who updated the entity
     */
    //#[ORM\Column(type: 'uuid', nullable: true)]
    //private ?Uuid $updatedBy;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = InvoiceStatus::NEW->value;
        $this->total = 0;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdAsString(): string
    {
        return $this->id->toRfc4122();
    }

    public function getDebtor(): Company
    {
        return $this->debtor;
    }

    public function setDebtor(Company $debtor): self
    {
        $this->debtor = $debtor;

        return $this;
    }

    public function getCreditor(): Company
    {
        return $this->creditor;
    }

    public function setCreditor(Company $creditor): self
    {
        $this->creditor = $creditor;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
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
