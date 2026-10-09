<?php

namespace App\Enums;

enum DebtDirection: string
{
    /** Someone owes me. */
    case Receivable = 'receivable';

    /** I owe someone. */
    case Payable = 'payable';

    public function label(): string
    {
        return match ($this) {
            self::Receivable => 'طلب',
            self::Payable => 'بدهی',
        };
    }
}
