<?php

namespace App\Enum;

enum ContractInvoiceTransactionTypeEnum: string
{
    case CREDIT = 'credit';

    case DEBIT = 'debit';
}
