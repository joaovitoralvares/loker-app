<?php

namespace App\Models\Vehicle;

use App\Casts\MoneyCast;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
