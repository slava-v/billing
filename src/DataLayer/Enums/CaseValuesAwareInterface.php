<?php

declare(strict_types=1);

namespace Billing\DataLayer\Enums;

interface CaseValuesAwareInterface
{
    /** @return array<int, string> */
    public static function casesValues(): array;
}
