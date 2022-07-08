<?php

declare(strict_types=1);

namespace Billing\DataLayer\Repository;

use Billing\DataLayer\Entity\Company;
use Billing\DataLayer\Entity\Invoice;
use Billing\DataLayer\Enums\InvoiceStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[] findAll()
 * @method Invoice[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface InvoiceRepositoryInterface
{
    public function add(Invoice $entity, bool $flush = false): void;

    public function remove(Invoice $entity, bool $flush = false): void;

    /** @return array<int, Invoice> */
    public function findByCreditor(Company $company, InvoiceStatus $status = InvoiceStatus::NEW, ?int $limit = 10): array;
}
