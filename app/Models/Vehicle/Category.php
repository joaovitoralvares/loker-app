<?php

namespace App\Models\Vehicle;

use App\Casts\MoneyCast;
use App\Models\Company;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'vehicle_categories';

    protected $fillable = [
        'company_id',
        'description',
        'daily_price'
    ];

    protected $casts = [
        'daily_price' => MoneyCast::class
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Str::upper($value),
            set: fn ($value) => Str::upper($value),
        );
    }
}
