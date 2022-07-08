<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Service;

use Billing\DataLayer\Entity\Invoice;
use Billing\Infrastructure\Dto\V1\Invoice as InvoiceDtoV1;
use Symfony\Component\Uid\Uuid;

interface InvoiceServiceInterface
{
    public function getInvoiceById(Uuid $invoiceId): Invoice;

    public function addInvoice(InvoiceDtoV1 $invoiceDto): Invoice;

    public function createFromJson(string $invoiceRequestDtoJson): InvoiceDtoV1;

    /**
     * Method will check if debtor total amount of open invoices PLUS the invoice total would exceed the
     * debtorLimit.
     *
     * @param Invoice $invoice
     * @param bool $shouldThrow Default FALSE, when TRUE will throw
     *                          \Billing\Infrastructure\Exception\DebtorLimitExceededException if limit exceeded
     * @return bool TRUE if the limit was exceeded, FALSE if limit wasn't exceeded
     */
    public function verifyOpenInvoiceLimitExceeded(Invoice $invoice, bool $shouldThrow = false): bool;
}
