<?php

namespace App\Enum;

enum SecurityDepositTransactionTypeEnum: string
{
    case CREDIT = 'credit';

    case DEBIT = 'debit';
}
