<?php

declare(strict_types=1);

namespace Billie\Tests\TestTrait;

use Billie\DataLayer\Entity\Company;
use Billie\DataLayer\Enums\CompanyStatus;
use Billie\Infrastructure\Dto\V1\Company as CompanyDtoV1;
use Faker\Factory as FakerFactory;
use Symfony\Component\Uid\Uuid;

/**
 * Trait is used in tests and creates the Company entity object or Company Dto object
 */
trait TestCompanyTrait
{
    private function createTestCompany(string $name = null): Company
    {
        // Trait could be used in integration test or unit tests
        $faker = method_exists($this, 'getFaker')
            ? $this->getFaker()
            : FakerFactory::create('de_DE');

        return (new Company())
            ->setName($name ?? $faker->company())
            ->setAddress($faker->address())
            ->setEmail($faker->email())
            ->setPhoneNumber($faker->phoneNumber())
            ->setIban($faker->bankAccountNumber())
            ->setBalance($faker->randomNumber())
            ->setDebtorLimit($faker->randomNumber())
            ->setAccessToken(md5(Uuid::v4()->toRfc4122()))
            ->setStatus(CompanyStatus::NEW->value);
    }

    private function createTestCompanyDto(string $name = null, Uuid|string $id = null): CompanyDtoV1
    {
        // Trait could be used in integration test or unit tests
        $faker = method_exists($this, 'getFaker')
            ? $this->getFaker()
            : FakerFactory::create('de_DE');

        $uuid = $id instanceof Uuid ?: Uuid::fromString($id ?: $faker->uuid());
        assert($uuid instanceof Uuid);

        return (new CompanyDtoV1())
            ->setName($name ?? $faker->company())
            ->setAddress($faker->address())
            ->setEmail($faker->email())
            ->setPhoneNumber($faker->phoneNumber())
            ->setIban($faker->bankAccountNumber())
            ->setBalance($faker->randomNumber())
            ->setDebtorLimit($faker->randomNumber())
            ->setAccessToken(md5(Uuid::v4()->toRfc4122()))
            ->setStatus(CompanyStatus::NEW->value)
            ->setId($uuid)
        ;
    }
}
