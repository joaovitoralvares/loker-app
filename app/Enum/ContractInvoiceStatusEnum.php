<?php

namespace App\Enum;

enum ContractInvoiceStatusEnum: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
}
