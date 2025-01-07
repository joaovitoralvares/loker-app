<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnerTransfer extends Model
{
    public $table = 'owner_transfers';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(OwnerTransaction::class, 'transfer_id');
    }
}
