<?php

declare(strict_types=1);

namespace Billing\DataLayer\Repository;

use Billing\DataLayer\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[] findAll()
 * @method Company[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface CompanyRepositoryInterface
{
    public function add(Company $entity, bool $flush = false): void;

    public function remove(Company $entity, bool $flush = false): void;

    public function findOneByName(string $name): ?Company;
}
