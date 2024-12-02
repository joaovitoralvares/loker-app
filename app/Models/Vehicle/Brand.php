<?php

namespace App\Models\Vehicle;

use App\Models\Company;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $table = 'vehicle_brands';
    protected $fillable = [
        'name',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }
}
