<?php

declare(strict_types=1);

namespace Billing\Infrastructure\Service;

use Billing\DataLayer\Entity\Invoice;
use Billing\DataLayer\Enums\InvoiceStatus;
use Billing\DataLayer\Repository\InvoiceRepositoryInterface;
use Billing\Infrastructure\Dto\V1\Invoice as InvoiceDtoV1;
use Billing\Infrastructure\Exception\DebtorLimitExceededException;
use Billing\Infrastructure\Exception\InvoiceCreateValidationException;
use Billing\Infrastructure\Exception\InvoiceNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InvoiceService implements InvoiceServiceInterface
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly CompanyServiceInterface $companyService,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function getInvoiceById(Uuid $invoiceId): Invoice
    {
        $invoice = $this->invoiceRepository->find($invoiceId);
        if (!$invoice instanceof Invoice) {
            throw new InvoiceNotFoundException($invoiceId->toRfc4122());
        }

        return $invoice;
    }

    public function addInvoice(InvoiceDtoV1 $invoiceDto): Invoice
    {
        $debtor = $this->companyService->getCompanyById($invoiceDto->getDebtor());
        $creditor = $this->companyService->getCompanyById($invoiceDto->getCreditor());

        $invoice = (new Invoice())
            ->setDebtor($debtor)
            ->setCreditor($creditor)
            ->setTotal($invoiceDto->getTotal())
            ->setSource($invoiceDto->getSource())
            ->setStatus($invoiceDto->getStatus());

        $this->invoiceRepository->add($invoice, true);

        return $invoice;
    }

    public function createFromJson(string $invoiceRequestDtoJson): InvoiceDtoV1
    {
        $invoiceDto = $this->serializer->deserialize($invoiceRequestDtoJson, InvoiceDtoV1::class, 'json');
        $errors = $this->validator->validate($invoiceDto);
        if ($errors->count() > 0) {
            throw new InvoiceCreateValidationException($errors);
        }

        $invoice = $this->addInvoice($invoiceDto);

        /**
         * Perform checks. When exceptions are thrown, they will be handled by
         * @see \Billing\Infrastructure\Events\EventSubscriber\ExceptionSubscriber
         */
        $this->verifyOpenInvoiceLimitExceeded($invoice, shouldThrow: true);

        // Upon reaching this line, if invoice passes validation/checks, the state will be open
        $invoice->setStatus(InvoiceStatus::OPEN->value);
        $this->invoiceRepository->add($invoice, true);

        assert($invoice->getId() instanceof Uuid);
        assert(is_string($invoice->getStatus()));

        $invoiceDto->setId($invoice->getId());
        $invoiceDto->setStatus($invoice->getStatus());

        return $invoiceDto;
    }

    /** @inheritdoc */
    public function verifyOpenInvoiceLimitExceeded(Invoice $invoice, bool $shouldThrow = false): bool
    {
        $totalOpenInvoicesAmount =
            $this->companyService->getTotalOpenInvoicesAmount($invoice->getDebtor())
            + $invoice->getTotal()
        ;
        if ($totalOpenInvoicesAmount > $invoice->getDebtor()->getDebtorLimit()) {
            $invoice->setStatus(InvoiceStatus::REJECTED->value);
            $this->invoiceRepository->add($invoice);
            if ($shouldThrow) {
                $amountExceeded = $totalOpenInvoicesAmount - $invoice->getDebtor()->getDebtorLimit();
                throw new DebtorLimitExceededException($invoice, $invoice->getDebtor(), $amountExceeded);
            }
            return true;
        }

        return false;
    }
}
