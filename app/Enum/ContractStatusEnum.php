<?php

namespace App\Enum;

enum ContractStatusEnum: string
{
    case WAITING_SIGNATURE = 'waiting_signature';
    case ACTIVE = 'active';
    case FINISHED = 'finished';
}
