<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Service;

use Billing\DataLayer\Entity\Company;
use Billing\DataLayer\Enums\CompanyStatus;
use Billing\DataLayer\Enums\InvoiceStatus;
use Billing\DataLayer\Repository\CompanyRepositoryInterface;
use Billing\DataLayer\Repository\InvoiceRepositoryInterface;
use Billing\Infrastructure\Dto\V1\Company as CompanyRequestDto;
use Billing\Infrastructure\Exception\CompanyCreateValidationException;
use Billing\Infrastructure\Exception\CompanyNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CompanyService implements CompanyServiceInterface
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function getCompanyByName(string $name): Company
    {
        $company = $this->companyRepository->findOneByName($name);
        if ($company === null) {
            throw new CompanyNotFoundException($name);
        }

        return $company;
    }

    public function getCompanyById(Uuid $id): Company
    {
        $company = $this->companyRepository->findOneBy(['id' => $id]);
        if ($company === null) {
            throw new CompanyNotFoundException($id->toRfc4122());
        }

        return $company;
    }

    public function addCompany(CompanyRequestDto $companyRequestDto): Company
    {
        /**
         * Not to leak the data between layers, let's map the dto object into entity object
         *
         * This can be replaced with object mapping solutions
         */
        $company = (new Company())
            ->setName($companyRequestDto->getName())
            ->setAddress($companyRequestDto->getAddress())
            ->setEmail($companyRequestDto->getEmail())
            ->setPhoneNumber($companyRequestDto->getPhoneNumber())
            ->setIban($companyRequestDto->getIban())
            ->setBalance($companyRequestDto->getBalance())
            ->setDebtorLimit($companyRequestDto->getDebtorLimit())
            ->setAccessToken($companyRequestDto->getAccessToken())
            ->setStatus($companyRequestDto->getStatus());

        $this->companyRepository->add($company, true);

        return $company;
    }

    public function createFromJson(string $companyRequestDtoJson): Company
    {
        $companyDto = $this->serializer->deserialize($companyRequestDtoJson, CompanyRequestDto::class, 'json');
        $errors = $this->validator->validate($companyDto);
        if ($errors->count() > 0) {
            throw new CompanyCreateValidationException($errors);
        }

        return $this->addCompany($companyDto);
    }

    public function getTotalOpenInvoicesAmount(Company $company): int
    {
        // Get debited / open invoices
        // This could be also, if needed, optimized through a single query in repository
        $openInvoices = $this->invoiceRepository->findByCreditor($company, InvoiceStatus::OPEN, null);
        $totalOpen = 0;
        foreach ($openInvoices as $openInvoice) {
            $totalOpen += $openInvoice->getTotal();
        }

        return $totalOpen;
    }
}
