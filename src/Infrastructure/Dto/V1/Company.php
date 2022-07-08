<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Dto\V1;

use Billing\DataLayer\Enums\CompanyStatus;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'CompanyAddRequest', required: ['name', 'address', 'email', 'phoneNumber', 'iban', 'balance', 'debtorLimit'])]
class Company
{
    private Uuid $id;

    #[OA\Property]
    private string $name;

    #[OA\Property]
    private string $address;

    #[Assert\Email]
    #[OA\Property]
    private string $email;

    #[OA\Property]
    private string $phoneNumber;

    #[Assert\Iban]
    #[OA\Property]
    private string $iban;

    #[OA\Property]
    private int $balance;

    #[OA\Property]
    private int $debtorLimit;

    private string $accessToken;

    #[Assert\Choice(
        callback: [CompanyStatus::class, 'casesValues'],
        message: 'The value {{ value }} you selected is not a valid choice for "Status" field'
    )]
    private string $status;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getIban(): string
    {
        return $this->iban;
    }

    public function setIban(string $iban): self
    {
        $this->iban = $iban;
        return $this;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
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

    public function getDebtorLimit(): int
    {
        return $this->debtorLimit;
    }

    public function setDebtorLimit(int $debtorLimit): self
    {
        $this->debtorLimit = $debtorLimit;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Company
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): Company
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
}
