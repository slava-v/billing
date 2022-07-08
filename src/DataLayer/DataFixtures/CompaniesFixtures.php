<?php

declare(strict_types=1);

namespace Billing\DataLayer\DataFixtures;

use Billing\DataLayer\Entity\Company;
use Billing\DataLayer\Enums\CompanyStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class CompaniesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('de_DE');

        for ($i = 0; $i < 20; $i++) {
            $manager->persist((new Company())
                ->setName($faker->company())
                ->setAddress($faker->address())
                ->setEmail($faker->email())
                ->setPhoneNumber($faker->phoneNumber())
                ->setIban($faker->bankAccountNumber())
                ->setBalance($faker->randomNumber())
                ->setDebtorLimit($faker->randomNumber())
                ->setAccessToken(md5(Uuid::v4()->toRfc4122()))
                ->setStatus(CompanyStatus::NEW->value));
        }
        $manager->flush();
    }
}
