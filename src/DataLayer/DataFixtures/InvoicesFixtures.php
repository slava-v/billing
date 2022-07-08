<?php

declare(strict_types=1);

namespace Billing\DataLayer\DataFixtures;

use Billing\DataLayer\Entity\Company;
use Billing\DataLayer\Entity\Invoice;
use Billing\DataLayer\Enums\CompanyStatus;
use Billing\DataLayer\Repository\CompanyRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class InvoicesFixtures extends Fixture
{
    public function __construct(
        private readonly CompanyRepositoryInterface $companyRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create('de_DE');
        $companies = $this->companyRepository->findAll();

        for ($i = 0; $i < 20; $i++) {
            $creditor = $this->pickRandomCompany($companies);
            $debtor = $this->pickRandomCompany($companies, $creditor);
            $manager->persist(
                (new Invoice())
                    ->setCreditor($creditor)
                    ->setDebtor($debtor)
                    ->setTotal($faker->randomNumber(3, true))
                    ->setStatus(CompanyStatus::NEW->value)
                    ->setSource($faker->ipv4())
            );
        }
        $manager->flush();
    }

    /** @param array<Company> $companies */
    private function pickRandomCompany(array $companies, ?Company $excludingCompany = null): Company
    {
        $selected = null;
        while ($selected === null || $selected === $excludingCompany) {
            $randomIndex = rand(0, count($companies) - 1);
            $selected = $companies[$randomIndex];
        }
        assert($selected instanceof Company);

        return $selected;
    }
}
