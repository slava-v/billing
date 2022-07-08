<?php

declare(strict_types=1);

namespace Billing\DataLayer\Enums;

trait CaseValuesTrait
{
    /** @return array<int, string> */
    public static function casesValues(): array
    {
        $values = [];
        foreach (static::cases() as $case) {
            $values[] = $case->value;
        }
        return $values;
    }
}
