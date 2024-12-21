<?php

namespace App\Models;

use App\Enum\ContractInvoiceStatusEnum;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    protected $table = 'contracts';

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
     * @return BelongsTo<Owner>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * @return BelongsTo<Vehicle>
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return HasMany<Invoice>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return HasMany<Invoice>
     */
    public function pendingPaymentInvoices(): HasMany
    {
        return $this->invoices()->whereNotIn('status', [ContractInvoiceStatusEnum::DRAFT->value, ContractInvoiceStatusEnum::PAID->value]);
    }
}
