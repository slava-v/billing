<?php

declare(strict_types=1);

namespace Billing\DataLayer\Enums;

enum InvoiceStatus: string implements CaseValuesAwareInterface
{
    use CaseValuesTrait;

    case NEW = 'NEW';
    case OPEN = 'OPEN';
    case PAID = 'PAID';
    case REFUNDED = 'REFUNDED';
    case REJECTED = 'REJECTED';
}
