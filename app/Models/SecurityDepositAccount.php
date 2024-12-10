<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecurityDepositAccount extends Model
{
    protected $table = 'security_deposit_accounts';

    /**
     * @return BelongsTo<Company>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<Customer>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<SecurityDepositTransaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(SecurityDepositTransaction::class, 'account_id');
    }

    /**
     * @return HasMany<SecurityDepositTransaction>
     */
    public function deposits(): HasMany
    {
        return $this->transactions()->where('type', 'credit');
    }
}
