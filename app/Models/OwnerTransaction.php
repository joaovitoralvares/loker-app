<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnerTransaction extends Model
{
    protected $table = 'owner_transactions';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(OwnerTransfer::class, 'transfer_id');
    }
}
