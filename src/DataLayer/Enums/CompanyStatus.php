<?php

declare(strict_types=1);

namespace Billing\DataLayer\Enums;

enum CompanyStatus: string implements CaseValuesAwareInterface
{
    use CaseValuesTrait;

    case ACTIVE = 'ACTIVE';
    case NEW = 'NEW';
    case PENDING = 'PENDING';
    case INACTIVE = 'INACTIVE';
}
