<?php

namespace App\Enum;

enum OwnerTransactionStatusEnum: string
{
    case PENDING = 'pending';

    case PAID = 'paid';
}
