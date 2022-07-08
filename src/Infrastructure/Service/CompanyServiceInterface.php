<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Service;

use Billing\DataLayer\Entity\Company;
use Billing\Infrastructure\Dto\V1\Company as CompanyRequestDto;
use Symfony\Component\Uid\Uuid;

interface CompanyServiceInterface
{
    public function getCompanyByName(string $name): Company;

    public function getCompanyById(Uuid $uuid): Company;

    public function addCompany(CompanyRequestDto $companyRequestDto): Company;

    public function createFromJson(string $companyRequestDtoJson): Company;

    public function getTotalOpenInvoicesAmount(Company $company): int;
}
