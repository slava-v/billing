<?php

declare(strict_types=1);

namespace Billie\Tests\TestTrait;

use Billie\DataLayer\Entity\Company;
use Billie\DataLayer\Entity\Invoice;
use Billie\DataLayer\Enums\CompanyStatus;
use Billie\Infrastructure\Dto\V1\Company as CompanyDtoV1;
use Billie\Infrastructure\Dto\V1\Invoice as InvoiceDtoV1;
use Faker\Factory as FakerFactory;
use Symfony\Component\Uid\Uuid;

/**
 * Trait is used in tests and creates the Invoice entity object or Invoice Dto object
 *
 * Trait could be used in integration test or unit tests
 */
trait TestInvoiceTrait
{
    private function createTestInvoice(Company $debtor = null, Company $creditor = null): Invoice
    {
        $faker = method_exists($this, 'getFaker')
            ? $this->getFaker()
            : FakerFactory::create('de_DE');

        return (new Invoice())
            ->setDebtor($debtor ?: $this->createTestCompany())
            ->setCreditor($creditor ?: $this->createTestCompany())
            ->setTotal($faker->randomNumber(2, true))
            ->setStatus(CompanyStatus::NEW->value);
    }

    private function createTestInvoiceDto(
        Uuid|string $debtor = null,
        Uuid|string $creditor = null,
        Uuid|string $id = null
    ): InvoiceDtoV1 {

        $faker = method_exists($this, 'getFaker')
            ? $this->getFaker()
            : FakerFactory::create('de_DE');

        $uuid = $id instanceof Uuid ? $id : Uuid::fromString($id ?: $faker->uuid());
        assert($uuid instanceof Uuid);

        $creditorId = $creditor instanceof Uuid ? $creditor : Uuid::fromString($creditor ?: $faker->uuid());
        assert($creditorId instanceof Uuid);

        $debtorId = $debtor instanceof Uuid ? $debtor : Uuid::fromString($debtor ?: $faker->uuid());
        assert($debtorId instanceof Uuid);

        return (new InvoiceDtoV1())
            ->setDebtor($debtorId)
            ->setCreditor($creditorId)
            ->setTotal($faker->randomNumber(2, true))
            ->setStatus(CompanyStatus::NEW->value)
            ->setSource($faker->ipv4())
            ->setId($uuid);
    }
}
