<?php

namespace App\Models\Vehicle;

use App\Casts\MoneyCast;
use App\Models\Company;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property-read int $id
 * @property int $company_id
 * @property string $description
 * @property int $daily_price
 */
class Category extends Model
{
    protected $table = 'vehicle_categories';

    protected $fillable = [
        'company_id',
        'description',
        'daily_price'
    ];

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return HasMany<Vehicle>
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * @return Attribute<string, string>
     */
    public function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }
}
