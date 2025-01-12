<?php

namespace App\Enum;

enum SecurityDepositTransactionStatusEnum: string
{
    case SCHEDULED = 'scheduled';
    case PAID = 'paid';
}
