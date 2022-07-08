<?php

declare(strict_types=1);

namespace Billing\DataLayer\Entity;

use Billing\DataLayer\Enums\CompanyStatus;
use Billing\DataLayer\Repository\CompanyRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: CompanyRepositoryInterface::class)]
#[OA\Schema(schema: 'Company')]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[OA\Property]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[OA\Property]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[OA\Property]
    private string $address;

    #[ORM\Column(type: 'string', length: 34)]
    #[OA\Property]
    private string $iban;

    #[ORM\Column(type: 'integer')]
    #[OA\Property]
    private int $balance;

    #[ORM\Column(type: 'integer')]
    #[OA\Property]
    private int $debtorLimit;

    #[ORM\Column(type: 'string', length: 100)]
    #[Ignore]
    private string $accessToken;

    /**
     * @todo add doctrine enum type, and validation of field values using this enum type
     */
    #[ORM\Column(type: 'string', length: 15, options: ['default' => CompanyStatus::NEW])]
    private string $status;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 20)]
    private string $phoneNumber;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt;

    /** @var Collection<int, Company> */
    #[ORM\OneToMany(mappedBy: 'creditor', targetEntity: Invoice::class)]
    #[Ignore]
    private Collection $creditedInvoices;

    /** @var Collection<int, Company> */
    #[ORM\OneToMany(mappedBy: 'debtor', targetEntity: Invoice::class)]
    #[Ignore]
    private Collection $debitedInvoices;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = CompanyStatus::NEW->value;
        $this->creditedInvoices = new ArrayCollection();
        $this->debitedInvoices = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdAsString(): string
    {
        return $this->id->toRfc4122();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getDebtorLimit(): ?int
    {
        return $this->debtorLimit;
    }

    public function setDebtorLimit(int $debtorLimit): self
    {
        $this->debtorLimit = $debtorLimit;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
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

    /** @return Collection<int, Company> */
    public function getDebitedInvoices(): Collection
    {
        return $this->debitedInvoices;
    }

    /** @param Collection<int, Company> $debitedInvoices */
    public function setDebitedInvoices(Collection $debitedInvoices): Company
    {
        $this->debitedInvoices = $debitedInvoices;
        return $this;
    }

    /** @return Collection<int, Company> */
    public function getCreditedInvoices(): Collection
    {
        return $this->creditedInvoices;
    }

    /** @param Collection<int, Company> $creditedInvoices */
    public function setCreditedInvoices(Collection $creditedInvoices): Company
    {
        $this->creditedInvoices = $creditedInvoices;
        return $this;
    }
}
