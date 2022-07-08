<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Repository;

use Billing\DataLayer\Entity\Company;
use Billing\DataLayer\Entity\Invoice;
use Billing\DataLayer\Enums\InvoiceStatus;
use Billing\DataLayer\Repository\InvoiceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[] findAll()
 * @method Invoice[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository implements InvoiceRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function add(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @inheritdoc */
    public function findByCreditor(
        Company $company,
        InvoiceStatus $status = InvoiceStatus::NEW,
        ?int $limit = 10
    ): array {
        $results = $this->createQueryBuilder('i')
            ->andWhere('i.creditor = :creditor')
            ->andWhere('i.status = :status')
            ->setParameter('creditor', $company)
            ->setParameter('status', $status->value)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        assert(is_array($results));
        return $results;
    }
}
