<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityDepositTransaction extends Model
{
    /**
     * @return BelongsTo<SecurityDepositAccount>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(SecurityDepositAccount::class);
    }

    /**
     * @return BelongsTo<Contract>
     */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
